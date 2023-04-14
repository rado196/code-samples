import { PaymentStatusEnum } from '@foreach-am/evan-base-constants';
import { AnyType, Nullable } from '@foreach-am/evan-base-library';
import { isProduction } from '@foreach-am/evan-base-server';
import Payment from '../../../models/Payment';
import { BasePaymentProvider, OnCompleteCallbackInterface } from '../../base';
import { bindingPaymentType, currencyCodes, languageCodes } from './constants';

function getField(details: AnyType, field: string) {
  const lower = field.substring(0, 1).toLowerCase() + field.substring(1);
  const upper = field.substring(0, 1).toUpperCase() + field.substring(1);

  return details[lower] || details[upper];
}

function isSuccessResponse(details: AnyType): boolean {
  const possibleKeys = ['resposneCode', 'responseCode'];

  for (const possibleKey of possibleKeys) {
    const value = getField(details, possibleKey);
    if (typeof value !== 'undefined') {
      return value === '00';
    }
  }

  return false;
}

class AmeriaBankProvider extends BasePaymentProvider {
  /** @override */
  protected getProviderName(): string {
    return 'AmeriaBank';
  }

  /** @override */
  protected isTestMode(): boolean {
    return !isProduction;
  }

  /** @override */
  protected getApiHost(): string {
    if (this.isTestMode()) {
      return 'https://servicestest.ameriabank.am';
    }

    return 'https://services.ameriabank.am';
  }

  /** @override */
  protected async buildOrderId(
    userId: number,
    previousPaymentsCount: number
  ): Promise<number> {
    if (this.isTestMode()) {
      return 2_546_211 + previousPaymentsCount;
    }

    return super.buildOrderId(userId, previousPaymentsCount);
  }

  /** @override */
  protected async refund(payment: Payment): Promise<void> {
    const refundUrl = this.buildUrl('/VPOS/api/VPOS/RefundPayment');
    const refundData = {
      ClientID: process.env.PAYMENT_AMERIABANK_CLIENT_ID,
      Username: process.env.PAYMENT_AMERIABANK_USERNAME,
      Password: process.env.PAYMENT_AMERIABANK_PASSWORD,
      Amount: payment.getDataValue('amount'),
      PaymentID: payment.getDataValue('providerTransactionId'),
    };

    await this.http().post(refundUrl, refundData);
  }

  /** @override */
  protected async complete(payment: Payment): Promise<Nullable<AnyType>> {
    const response = await this.getDetails(payment);
    if (response.type === 'success') {
      return response.body;
    }

    return null;
  }

  /** @override */
  async initialize(walletId: number) {
    const payment = await this.initializePayment(walletId);

    const opaqueData: AnyType = {
      paymentId: payment.getDataValue('id'),
      transactionId: this.getTransactionId(),
      orderId: this.getOrderId(),
      userId: this.getUserId(),
      walletId: this.getWalletId(),
    };

    const initializeUrl = this.buildUrl('/VPOS/api/VPOS/InitPayment');
    const initializeData: AnyType = {
      ClientID: process.env.PAYMENT_AMERIABANK_CLIENT_ID,
      Username: process.env.PAYMENT_AMERIABANK_USERNAME,
      Password: process.env.PAYMENT_AMERIABANK_PASSWORD,
      Amount: this.getAmountModeBased(),
      Currency: currencyCodes[this.getCurrency()],
      Description: this.getDescription(),
      OrderID: this.getOrderId(),
      BackURL: process.env.PAYMENT_AMERIABANK_CALLBACK_URL,
      Timeout: 600, // 10 minute
      Opaque: JSON.stringify(opaqueData),
    };

    const cardHolderId = this.getCardHolderId();
    if (cardHolderId) {
      initializeData.CardHolderID = cardHolderId;
    }

    const response = await this.http().post(initializeUrl, initializeData);
    if (response.type === 'success') {
      const result = response.body;

      payment.setDataValue(
        'providerTransactionId',
        getField(result, 'PaymentID')
      );
      payment.setDataValue(
        'providerResponseMessage',
        getField(result, 'ResponseMessage')
      );
      payment.setDataValue(
        'providerResponseCode',
        getField(result, 'ResponseCode')
      );

      if (getField(result, 'ResponseMessage') !== 'OK') {
        payment.setDataValue('status', PaymentStatusEnum.Failure);
        await payment.save({
          transaction: this.transaction,
        });

        return this.respondError(
          getField(result, 'ResponseMessage'),
          getField(result, 'ResponseCode')
        );
      } else {
        payment.setDataValue('status', PaymentStatusEnum.Pending);
        await payment.save({
          transaction: this.transaction,
        });

        const redirectUrl = this.buildFullUrl('/VPOS/Payments/Pay', {
          lang: languageCodes[this.getLanguage()],
          id: getField(result, 'PaymentID'),
        });

        return this.respondRedirect(redirectUrl);
      }
    }

    payment.setDataValue('status', PaymentStatusEnum.Failure);
    await payment.save({
      transaction: this.transaction,
    });

    return this.respondError('UnknownError', -1);
  }

  /** @override */
  async bindingPayment(walletId: number, cardHolderId: string) {
    const payment = await this.initializePayment(walletId);

    const opaqueData: AnyType = {
      paymentId: payment.getDataValue('id'),
      transactionId: this.getTransactionId(),
      orderId: this.getOrderId(),
      userId: this.getUserId(),
      walletId: this.getWalletId(),
    };

    const bindingUrl = this.buildUrl('/VPOS/api/VPOS/MakeBindingPayment');
    const bindingData = {
      ClientID: process.env.PAYMENT_AMERIABANK_CLIENT_ID,
      Username: process.env.PAYMENT_AMERIABANK_USERNAME,
      Password: process.env.PAYMENT_AMERIABANK_PASSWORD,
      Amount: this.getAmountModeBased(),
      Currency: currencyCodes[this.getCurrency()],
      Description: this.getDescription(),
      OrderID: this.getOrderId(),
      BackURL: process.env.PAYMENT_AMERIABANK_CALLBACK_URL,
      PaymentType: bindingPaymentType.Binding,
      CardHolderId: cardHolderId,
      Opaque: JSON.stringify(opaqueData),
    };

    const response = await this.http().post(bindingUrl, bindingData);
    if (response.type === 'success') {
      const details = response.body;

      payment.setDataValue(
        'providerTransactionId',
        getField(details, 'PaymentID')
      );
      payment.setDataValue(
        'providerResponseMessage',
        getField(details, 'ResponseMessage')
      );
      payment.setDataValue(
        'providerResponseCode',
        getField(details, 'ResponseCode')
      );

      if (isSuccessResponse(details)) {
        payment.setDataValue('status', PaymentStatusEnum.Success);
        await this.updateWalletBalance(opaqueData['walletId'], payment);
      } else {
        payment.setDataValue('status', PaymentStatusEnum.Failure);
      }

      payment.setDataValue(
        'providerDescription',
        (getField(details, 'description') || '').trim()
      );
      await payment.save({
        transaction: this.transaction,
      });

      return payment.getDataValue('status') === PaymentStatusEnum.Success
        ? this.respondSuccess()
        : this.respondError();
    }

    payment.setDataValue('status', PaymentStatusEnum.Failure);
    await payment.save({
      transaction: this.transaction,
    });

    return this.respondError('UnknownError', -1);
  }

  async confirm(
    details: AnyType,
    onCompleteData?: OnCompleteCallbackInterface
  ) {
    const opaqueData = JSON.parse(getField(details, 'opaque'));
    const payment = await this.findPaymentById(
      opaqueData['paymentId'],
      opaqueData['orderId'],
      opaqueData['transactionId']
    );

    if (!payment) {
      return PaymentStatusEnum.Failure;
    }

    if (payment.getDataValue('status') === PaymentStatusEnum.Pending) {
      if (isSuccessResponse(details)) {
        payment.setDataValue('status', PaymentStatusEnum.Success);
        const completeData =
          await this.completeTransactionAndUpdateWalletBalance(
            opaqueData['walletId'],
            payment
          );

        if (
          typeof onCompleteData === 'function' &&
          completeData &&
          typeof completeData === 'object'
        ) {
          const cardNumber = !/^\d{6}\*{2}\d{4}$/.test(completeData.CardNumber)
            ? completeData.CardNumber
            : [
                completeData.CardNumber.substring(0, 4),
                '****',
                '****',
                completeData.CardNumber.substring(
                  completeData.CardNumber.length - 4
                ),
              ].join(' ');

          await onCompleteData({
            cardHolderId: completeData.CardHolderID,
            cardNumber: cardNumber,
            clientName: completeData.ClientName,
            expireDate: completeData.ExpDate,
          });
        }
      } else {
        payment.setDataValue('status', PaymentStatusEnum.Failure);
      }

      payment.setDataValue(
        'providerDescription',
        (getField(details, 'Description') || '').trim()
      );
      payment.setDataValue(
        'processingIp',
        (getField(details, 'ProcessingIP') || '').trim()
      );
      await payment.save({
        transaction: this.transaction,
      });
    }

    return payment.getDataValue('status');
  }

  private getDetails(payment: Payment) {
    const initializeUrl = this.buildUrl('/VPOS/api/VPOS/GetPaymentDetails');
    const initializeData = {
      Username: process.env.PAYMENT_AMERIABANK_USERNAME,
      Password: process.env.PAYMENT_AMERIABANK_PASSWORD,
      PaymentID: payment.getDataValue('providerTransactionId'),
    };

    return this.http().post(initializeUrl, initializeData);
  }

  private getAmountModeBased() {
    if (this.isTestMode()) {
      return 10;
    }

    return this.getAmount();
  }
}

export default AmeriaBankProvider;

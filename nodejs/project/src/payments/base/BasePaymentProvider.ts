import {
  PaymentCurrencyEnum,
  PaymentLanguageEnum,
  PaymentStatusEnum,
  PaymentCreditCardEnum,
} from '@foreach-am/evan-base-constants';
import {
  services as libraryServices,
  AnyType,
  Nullable,
  Primitive,
} from '@foreach-am/evan-base-library';
import { Transaction } from 'sequelize';
import Payment from '../../models/Payment';
import PaymentHttpLog from '../../models/PaymentHttpLog';
import Wallet from '../../models/Wallet';
import InitializationResponse from './InitializationResponse';
import PaymentHttpClient from './PaymentHttpClient';

export interface OnCompleteDataInterface {
  cardHolderId: string;
  cardNumber: string;
  clientName: string;
  expireDate: string;
}

export interface OnCompleteCallbackInterface {
  (data: OnCompleteDataInterface): Promise<void>;
}

abstract class BasePaymentProvider {
  getCreditCardTypeByNumber(creditCardNumber: string): PaymentCreditCardEnum {
    if (creditCardNumber.startsWith('5')) {
      return PaymentCreditCardEnum.MasterCard;
    }
    if (creditCardNumber.startsWith('4')) {
      return PaymentCreditCardEnum.Visa;
    }
    if (
      creditCardNumber.startsWith('34') ||
      creditCardNumber.startsWith('37')
    ) {
      return PaymentCreditCardEnum.AmericanExpress;
    }
    if (creditCardNumber.startsWith('6')) {
      return PaymentCreditCardEnum.Discover;
    }

    return PaymentCreditCardEnum.Unknown;
  }

  abstract initialize(walletId: number): Promise<AnyType>;
  abstract bindingPayment(
    walletId: number,
    cardHolderId: string
  ): Promise<AnyType>;
  protected abstract isTestMode(): boolean;
  protected abstract getProviderName(): string;
  protected abstract getApiHost(): string;
  protected abstract refund(payment: Payment): Promise<void>;
  protected abstract complete(
    payment: Payment,
    onCompleteData?: OnCompleteCallbackInterface
  ): Promise<Nullable<AnyType>>;

  protected getAmountWithBonus(amount: number) {
    // const bonusPercent = 20;
    const bonusPercent = 0;

    return amount * (1 + bonusPercent / 100);
  }

  protected http() {
    return new PaymentHttpClient(this.getApiHost());
  }

  protected buildUrl(url: string, queryParams: Record<string, Primitive> = {}) {
    for (const [key, value] of Object.entries(queryParams)) {
      const glue = url.indexOf('?') !== -1 ? '&' : '?';
      url += `${glue}${key}=${value}`;
    }

    return url;
  }

  protected buildFullUrl(
    url: string,
    queryParams: Record<string, Primitive> = {}
  ) {
    const hostname = this.getApiHost();
    const endpoint = this.buildUrl(url, queryParams);

    return `${hostname}${endpoint}`;
  }

  protected async buildOrderId(
    userId: number,
    previousPaymentsCount: number
  ): Promise<number> {
    return userId * 100_000 + previousPaymentsCount;
  }

  private async buildPaymentOrderId(userId: number): Promise<number> {
    const paymentsCount = await Payment.count({
      where: {
        userId: userId,
      },
    });

    return this.buildOrderId(userId, paymentsCount);
  }

  private buildDescription(
    userId: number,
    userFirstName: Nullable<string>,
    userLastName: Nullable<string>
  ) {
    const prefix = 'Re-fill EVAN Balance';

    const fullName = [userFirstName, userLastName]
      .filter((value: Nullable<string>) => !!value)
      .join(' ');

    if (fullName) {
      return `${prefix} - ${fullName}`;
    }

    return `${prefix} - UserID ${userId}`;
  }

  constructor(
    protected readonly paymentHttpLog: Nullable<PaymentHttpLog>,
    protected readonly transaction: Nullable<Transaction> = null
  ) {}

  private userId: Nullable<number> = null;
  private userFirstName: Nullable<string> = null;
  private userLastName: Nullable<string> = null;
  private walletId: Nullable<number> = null;
  private amount: Nullable<number> = null;
  private bonus: Nullable<number> = null;
  private amountWithBonus: Nullable<number> = null;
  private description: Nullable<string> = null;
  private currency: Nullable<PaymentCurrencyEnum> = null;
  private language: Nullable<PaymentLanguageEnum> = null;
  private transactionId: Nullable<string> = null;
  private orderId: Nullable<number> = null;
  private status: Nullable<PaymentStatusEnum> = null;
  private cardHolderId: Nullable<string> = null;

  getUserId() {
    return this.userId!;
  }

  setUserId(userId: number) {
    this.userId = userId;
    return this;
  }

  getUserFirstName() {
    return this.userFirstName!;
  }

  setUserFirstName(userFirstName: string) {
    this.userFirstName = userFirstName;
    return this;
  }

  getUserLastName() {
    return this.userLastName!;
  }

  setUserLastName(userLastName: string) {
    this.userLastName = userLastName;
    return this;
  }

  getWalletId() {
    return this.walletId!;
  }

  setWalletId(walletId: number) {
    this.walletId = walletId;
    return this;
  }

  getAmount() {
    return this.amount!;
  }

  setAmount(amount: number) {
    this.amount = amount;
    this.amountWithBonus = this.getAmountWithBonus(amount);
    this.bonus = this.amountWithBonus - this.amount;

    return this;
  }

  getCurrency() {
    // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-assertion
    return this.currency!;
  }

  setCurrency(currency: PaymentCurrencyEnum) {
    this.currency = currency;
    return this;
  }

  getDescription() {
    return this.description!;
  }

  setDescription(description: string) {
    this.description = description;
    return this;
  }

  getLanguage() {
    // eslint-disable-next-line @typescript-eslint/no-unnecessary-type-assertion
    return this.language!;
  }

  setLanguage(language: PaymentLanguageEnum) {
    this.language = language;
    return this;
  }

  getTransactionId() {
    return this.transactionId!;
  }

  getOrderId() {
    return this.orderId!;
  }

  getCardHolderId() {
    return this.cardHolderId;
  }

  setCardHolderId(cardHolderId: string) {
    this.cardHolderId = cardHolderId;
    return this;
  }

  protected async initializePayment(walletId: number): Promise<Payment> {
    this.transactionId = libraryServices.RandomService.uuid();
    this.currency = this.currency || PaymentCurrencyEnum.USD;
    this.language = this.language || PaymentLanguageEnum.ENG;
    this.status = PaymentStatusEnum.Pending;

    this.orderId = await this.buildPaymentOrderId(this.userId!);
    this.walletId = walletId;
    this.description = this.buildDescription(
      this.userId!,
      this.userFirstName,
      this.userLastName
    );

    const payment = await Payment.create({
      provider: this.getProviderName(),
      userId: this.userId!,
      walletId: this.walletId,
      amount: this.amount!,
      amountWithBonus: this.amountWithBonus!,
      bonus: this.bonus!,
      currency: this.currency,
      language: this.language,
      status: this.status,
      description: this.description,
      orderId: this.orderId,
      transactionId: this.transactionId,
    });

    return payment;
  }

  protected async updateWalletBalance(
    walletId: number,
    payment: Payment
  ): Promise<boolean> {
    const wallet = await Wallet.findByPk(walletId);
    if (wallet) {
      await wallet.increment('balance', {
        by: payment.getDataValue('amountWithBonus'),
      });

      return true;
    }

    await this.refund(payment);

    payment.setDataValue('status', PaymentStatusEnum.Refunded);
    await payment.save({
      transaction: this.transaction,
    });

    return false;
  }

  protected async completeTransactionAndUpdateWalletBalance(
    walletId: number,
    payment: Payment
  ): Promise<boolean | AnyType> {
    const completeData = await this.complete(payment);
    const result = await this.updateWalletBalance(walletId, payment);

    if (!result) {
      return result;
    }

    return completeData;
  }

  protected async findPaymentById(
    paymentId: number,
    orderId: number,
    transactionId: string
  ) {
    const payment = await Payment.findOne({
      where: {
        orderId: orderId,
        transactionId: transactionId,
        id: paymentId,
      },
    });

    return payment;
  }

  protected respondError(message = 'unknown', code = 1) {
    return InitializationResponse.factory()
      .setStatusError()
      .setErrorMessage(message)
      .setErrorCode(code)
      .build();
  }

  protected respondRedirect(url: string) {
    return InitializationResponse.factory()
      .setStatusRedirect()
      .setRedirectUrl(url)
      .build();
  }

  protected respondSubmit(url: string, method: string, data: AnyType) {
    return InitializationResponse.factory()
      .setStatusFormSubmit()
      .setFormSubmitMethod(method)
      .setFormSubmitUrl(url)
      .setFormSubmitData(data)
      .build();
  }

  protected respondSuccess() {
    return InitializationResponse.factory().setStatusSuccess().build();
  }
}

export default BasePaymentProvider;

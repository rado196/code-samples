import {
  PaymentMethodEnum,
  PaymentStatusEnum,
  UserRoleEnum,
} from '@foreach-am/evan-base-constants';
import {
  Nullable,
  services as libraryServices,
  helpers as libraryHelpers,
} from '@foreach-am/evan-base-library';
import {
  ExpressRequest,
  ExpressResponse,
  BaseController,
  route,
} from '@foreach-am/evan-base-server';
import { Transaction } from 'sequelize';
import PaymentHttpLog from '../../models/PaymentHttpLog';
import PaymentMethod from '../../models/PaymentMethod';
import Wallet from '../../models/Wallet';
import { AmeriaBankProvider } from '../../payments/providers';

function getCreditCardProvider(
  paymentHttpLog: Nullable<PaymentHttpLog>,
  transaction: Nullable<Transaction> = null
) {
  return new AmeriaBankProvider(paymentHttpLog, transaction);
}

class CreditCardController extends BaseController {
  constructor() {
    super('Payment.Provider.CreditCardController');
  }

  @route.post('/providers/credit-card/initialize', [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
    'payments.log',
  ])
  async initialize(req: ExpressRequest, res: ExpressResponse) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;

      const userResponse = await super
        .internal()
        .callGet(
          this.internal().domainApi(),
          `/api/users/users/${userId}/internal`
        );

      if (!userResponse) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const user = userResponse.body.data.user;

      const wallet = await Wallet.findOne({
        where: {
          userId: userId,
          isBusiness: false,
        },
        transaction: transaction,
      });

      if (!wallet) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const provider = getCreditCardProvider(req.paymentHttpLog, transaction)
        .setAmount(req.body.attachCard ? 100 : req.body.amount)
        .setCurrency(req.body.currency)
        .setUserId(userId)
        .setUserFirstName(user.firstName)
        .setUserLastName(user.lastName);

      if (req.body.attachCard) {
        const cardHolderId = libraryServices.RandomService.uuid();
        await PaymentMethod.create(
          {
            userId: userId,
            paymentMethod: PaymentMethodEnum.CreditCard, // @TODO: maybe visa
            cardHolderId: cardHolderId,
            isActive: false,
          },
          {
            transaction: transaction,
          }
        );

        provider.setCardHolderId(cardHolderId);
      }

      const walletId = wallet.getDataValue('id');
      const response = await provider.initialize(walletId);

      if (response.status === 'error') {
        libraryHelpers.log.error('Init Payment Error:', response);
        await transaction.rollback();
      } else {
        await transaction.commit();
      }

      this.ok(req, res, response);
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.get('/providers/credit-card/callback', [
    // route.middleware.api(),
    'payments.log',
  ])
  async callbackUrl(req: ExpressRequest, res: ExpressResponse) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const provider = getCreditCardProvider(req.paymentHttpLog, transaction);
      const status = await provider.confirm(req.query, async (completeData) => {
        if (completeData.cardHolderId) {
          const attachedCard = await PaymentMethod.findOne({
            where: {
              cardHolderId: completeData.cardHolderId,
            },
            transaction: transaction,
          });

          if (attachedCard) {
            attachedCard.setDataValue('cardNumber', completeData.cardNumber);
            attachedCard.setDataValue('clientName', completeData.clientName);
            attachedCard.setDataValue('expireDate', completeData.expireDate);
            attachedCard.setDataValue('isActive', true);

            attachedCard.setDataValue(
              'creditCardType',
              provider.getCreditCardTypeByNumber(completeData.cardNumber)
            );

            await attachedCard.save({
              transaction: transaction,
            });
          }
        }
      });

      if (status !== PaymentStatusEnum.Success) {
        throw new Error('Payment failed.');
      }

      await transaction.commit();
      this.renderView(req, res, 'payment-result', {
        status: 'success',
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      this.renderView(req, res, 'payment-result', {
        status: 'failure',
      });
    }
  }

  @route.post(`/providers/credit-card/binding/${route.params.id('methodId')}`, [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
    'payments.log',
  ])
  async makeBindingPayment(
    req: ExpressRequest<{ methodId: number }>,
    res: ExpressResponse
  ) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;

      const userResponse = await super
        .internal()
        .callGet(
          this.internal().domainApi(),
          `/api/users/users/${userId}/internal`
        );

      if (!userResponse) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const user = userResponse.body.data.user;

      const wallet = await Wallet.findOne({
        where: {
          userId: userId,
          isBusiness: false,
        },
        transaction: transaction,
      });

      if (!wallet) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const attachedCard = await PaymentMethod.findOne({
        where: {
          paymentMethod: PaymentMethodEnum.CreditCard,
          userId: userId,
          id: req.params.methodId,
        },
        transaction: transaction,
      });

      if (!attachedCard) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const provider = getCreditCardProvider(req.paymentHttpLog)
        .setAmount(req.body.amount)
        .setCurrency(req.body.currency)
        .setUserId(userId)
        .setUserFirstName(user.firstName)
        .setUserLastName(user.lastName);

      const walletId = wallet.getDataValue('id');
      const cardHolderId = attachedCard.getDataValue('cardHolderId');

      if (!cardHolderId) {
        await transaction.rollback();
        return this.unauthorized(req, res, ['access_token:invalid']);
      }

      const response = await provider.bindingPayment(walletId, cardHolderId);

      if (response.status === 'error') {
        await transaction.rollback();
      } else {
        await transaction.commit();
      }

      this.ok(req, res, response);
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }
}

export default CreditCardController;

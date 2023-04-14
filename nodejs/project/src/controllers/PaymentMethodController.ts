import {
  PaymentMethodEnum,
  UserRoleEnum,
} from '@foreach-am/evan-base-constants';
import {
  AnyType,
  helpers as libraryHelpers,
} from '@foreach-am/evan-base-library';
import {
  ExpressRequest,
  ExpressResponse,
  BaseController,
  route,
} from '@foreach-am/evan-base-server';
import PaymentMethod from '../models/PaymentMethod';

class PaymentMethodController extends BaseController {
  constructor() {
    super('Payment.PaymentMethodController');
  }

  @route.get('/payment-methods', [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
  ])
  async getPaymentMethods(req: ExpressRequest, res: ExpressResponse) {
    const { id: userId } = this.headers().getDecodedAccessToken(req)!;
    const transaction = await this.transaction().buildTransaction();

    try {
      const condition: AnyType = {
        userId: userId,
        isActive: true,
      };

      this.model().appendConditionFromQueryParam(req, condition, 'isActive');
      this.model().appendConditionFromQueryParam(req, condition, 'isDefault');
      this.model().appendConditionFromQueryParam(
        req,
        condition,
        'paymentMethod'
      );
      this.model().appendConditionFromQueryParam(
        req,
        condition,
        'creditCardType'
      );

      const paymentMethods = await PaymentMethod.findAll({
        plain: false,
        where: condition,
        attributes: {
          exclude: ['cardHolderId'],
        },
        transaction: transaction,
      });

      await transaction.commit();
      this.ok(req, res, {
        paymentMethods: paymentMethods || [],
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.get(`/payment-methods/${route.params.id()}`, [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
  ])
  async getPaymentMethodById(
    req: ExpressRequest<{ id: number }>,
    res: ExpressResponse
  ) {
    const { id: userId } = this.headers().getDecodedAccessToken(req)!;
    const transaction = await this.transaction().buildTransaction();

    try {
      const paymentMethod = await PaymentMethod.findOne({
        where: {
          id: req.params.id,
          userId: userId,
        },
        attributes: {
          exclude: ['cardHolderId'],
        },
        transaction: transaction,
      });

      if (!paymentMethod) {
        await transaction.rollback();
        return this.notFound(req, res, ['not_found:payment_method']);
      }

      await transaction.commit();
      this.ok(req, res, {
        paymentMethod: paymentMethod,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.delete(`/payment-methods/${route.params.id()}`, [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
  ])
  async deletePaymentMethod(
    req: ExpressRequest<{ id: number }>,
    res: ExpressResponse
  ) {
    const { id: userId } = this.headers().getDecodedAccessToken(req)!;
    const transaction = await this.transaction().buildTransaction();

    try {
      const paymentMethod = await PaymentMethod.findOne({
        where: {
          id: req.params.id,
          userId: userId,
        },
        transaction: transaction,
      });

      if (!paymentMethod) {
        await transaction.rollback();
        return this.notFound(req, res, ['not_found:payment_method']);
      }

      if (
        paymentMethod.getDataValue('paymentMethod') ===
        PaymentMethodEnum.EvanWallet
      ) {
        await transaction.rollback();
        return this.notFound(req, res, ['denied:wallet_delete']);
      }

      await paymentMethod.destroy({
        force: true,
        transaction: transaction,
      });

      await transaction.commit();
      this.ok(req, res, {
        paymentMethod: paymentMethod,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }
}

export default PaymentMethodController;

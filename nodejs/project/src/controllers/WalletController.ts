import { UserRoleEnum } from '@foreach-am/evan-base-constants';
import { helpers as libraryHelpers } from '@foreach-am/evan-base-library';
import {
  ExpressRequest,
  ExpressResponse,
  BaseController,
  route,
} from '@foreach-am/evan-base-server';
import Wallet from '../models/Wallet';

class WalletController extends BaseController {
  constructor() {
    super('Payment.WalletController');
  }

  @route.get('/wallets', [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.SuperAdmin, UserRoleEnum.Admin, UserRoleEnum.User],
    }),
  ])
  async getWallets(req: ExpressRequest, res: ExpressResponse) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const condition: AnyType = {};

      const user = this.headers().getDecodedAccessToken(req)!;
      if (user.role === UserRoleEnum.User) {
        condition.userId = user.id;
      } else {
        this.model().appendConditionFromQueryParam(req, condition, 'userId');
        this.model().appendConditionFromQueryParam(
          req,
          condition,
          'isBusiness'
        );
      }

      if (
        !condition.userId ||
        (Array.isArray(condition.userId) && condition.userId.length === 0)
      ) {
        await transaction.rollback();
        return this.badRequest(req, res, ['wallet:no_user_id_provided']);
      }

      const wallets = await Wallet.findAll({
        plain: false,
        where: condition,
        transaction: transaction,
      });

      await transaction.commit();
      this.ok(req, res, {
        wallets: wallets || [],
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.get(`/wallets/${route.params.id()}`, [
    route.middleware.api(),
    route.middleware.auth({
      roles: [UserRoleEnum.User],
    }),
  ])
  async getWalletById(
    req: ExpressRequest<{ id: number }>,
    res: ExpressResponse
  ) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;
      const wallet = await Wallet.findOne({
        where: {
          id: req.params.id,
          userId: userId,
        },
        transaction: transaction,
      });

      if (!wallet) {
        await transaction.rollback();
        return this.notFound(req, res, ['not_found:wallet']);
      }

      await transaction.commit();
      this.ok(req, res, {
        wallet: wallet,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.post('/wallets', [
    route.middleware.api({
      isInternal: true,
    }),
  ])
  async createWallet(req: ExpressRequest, res: ExpressResponse) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const wallet = await Wallet.create(
        {
          userId: req.body.userId,
          isBusiness: req.body.isBusiness,
          balance: 0,
        },
        {
          transaction: transaction,
        }
      );

      await transaction.commit();
      this.created(req, res, {
        wallet: wallet,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.patch(
    `/wallets/${route.params.build('type', 'business|personal')}/fill`,
    [
      route.middleware.api({
        isInternal: true,
      }),
      route.middleware.auth({
        roles: [UserRoleEnum.User],
      }),
    ]
  )
  async fillWalletBalance(
    req: ExpressRequest<{ type: 'business' | 'personal' }>,
    res: ExpressResponse
  ) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;
      const wallet = await Wallet.findOne({
        where: {
          userId: userId,
          isBusiness: req.params.type === 'business',
        },
        transaction: transaction,
      });

      if (!wallet) {
        await transaction.rollback();
        return this.notFound(req, res, ['not_found:wallet']);
      }

      await wallet.increment('balance', {
        by: req.body.amount,
        transaction: transaction,
      });

      await transaction.commit();
      this.created(req, res, {
        wallet: wallet,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }

  @route.patch(
    `/wallets/${route.params.build('type', 'business|personal')}/change`,
    [
      route.middleware.api({
        isInternal: true,
      }),
      route.middleware.auth({
        roles: [UserRoleEnum.User],
      }),
    ]
  )
  async chargeFromWallet(
    req: ExpressRequest<{ type: 'business' | 'personal' }>,
    res: ExpressResponse
  ) {
    const transaction = await this.transaction().buildTransaction();

    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;
      const wallet = await Wallet.findOne({
        where: {
          userId: userId,
          isBusiness: req.params.type === 'business',
        },
        transaction: transaction,
      });

      if (!wallet) {
        await transaction.rollback();
        return this.notFound(req, res, ['not_found:wallet']);
      }

      await wallet.decrement('balance', {
        by: req.body.amount,
        transaction: transaction,
      });

      await transaction.commit();
      this.created(req, res, {
        wallet,
      });
    } catch (e) {
      libraryHelpers.log.error(e);
      await transaction.rollback();

      return this.internalServer(req, res);
    }
  }
}

export default WalletController;

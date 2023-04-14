import {
  ExpressRequest,
  ExpressResponse,
  BaseController,
  route,
} from '@foreach-am/evan-base-server';
import ChargingTransaction from '../models/ChargingTransaction';
import Wallet from '../models/Wallet';

class ChargingTransactionController extends BaseController {
  constructor() {
    super('Payment.ChargingTransactionController');
  }

  @route.post('/charging-transactions', [
    route.middleware.api({
      isInternal: true,
    }),
  ])
  async chargeTransaction(req: ExpressRequest, res: ExpressResponse) {
    const wallet = await Wallet.findOne({
      where: {
        userId: req.body.userId,
        isBusiness: false,
      },
    });

    if (!wallet) {
      return this.ok(req, res, {
        status: 'failure',
        code: 'wallet_not_found',
        amountApplied: 0,
      });
    }

    let currentBalance = wallet.getDataValue('balance');
    currentBalance -= req.body.amount;

    wallet.setDataValue('balance', currentBalance);
    await wallet.save();

    if (currentBalance <= 0 && !req.body.allowNegativeBalance) {
      return this.ok(req, res, {
        status: 'failure',
        code: 'insufficient_balance',
        amountApplied: req.body.amount,
      });
    }

    await ChargingTransaction.create({
      amount: req.body.amount,
      userId: req.body.userId,
      transactionId: req.body.transactionId,
      stationId: req.body.stationId,
    });

    return this.ok(req, res, {
      status: 'success',
      code: 'success',
      amountApplied: req.body.amount,
    });
  }
}

export default ChargingTransactionController;

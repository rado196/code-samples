import { AnyType } from '@foreach-am/evan-base-library';
import {
  BaseMiddleware,
  ExpressNext,
  ExpressRequest,
  ExpressResponse,
} from '@foreach-am/evan-base-server';
import PaymentHttpLog, { HttpMethodEnum } from '../models/PaymentHttpLog';

export const middlewareName = 'payments.log';

class PaymentHttpLoggerMiddleware extends BaseMiddleware {
  constructor() {
    super(middlewareName);
  }

  private async getUserId(req: ExpressRequest) {
    try {
      const { id: userId } = this.headers().getDecodedAccessToken(req)!;
      return userId;
    } catch (e) {
      return null;
    }
  }

  private async createLog(req: ExpressRequest) {
    const userId = await this.getUserId(req);

    const protocol = req.protocol;
    const host = req.hostname;
    const url = req.originalUrl;
    const fullUrl = `${protocol}://${host}${url}`;

    const paymentHttpLog = await PaymentHttpLog.create({
      userId: userId as number | null,
      url: fullUrl,
      httpMethod: req.method as HttpMethodEnum,
      requestBody: req.body,
      requestHeaders: req.headers,
    });

    return paymentHttpLog;
  }

  async handle(
    req: ExpressRequest,
    res: ExpressResponse,
    next: ExpressNext
  ): Promise<AnyType> {
    const paymentHttpLog = await this.createLog(req);
    req.paymentHttpLog = paymentHttpLog;

    const originalSend = res.json;
    const sendResponse = async (response: AnyType) => {
      paymentHttpLog.setDataValue('responseBody', response);
      paymentHttpLog.setDataValue('responseHeaders', res.getHeaders());
      paymentHttpLog.setDataValue('statusCode', res.statusCode);

      await paymentHttpLog.save();

      return originalSend.call(res, response);
    };

    res.json = sendResponse;
    return next();
  }
}

export default PaymentHttpLoggerMiddleware;

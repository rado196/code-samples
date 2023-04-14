import { PaymentStatusEnum } from '@foreach-am/evan-base-constants';
import { BaseJob, JobTimer } from '@foreach-am/evan-base-server';
import Payment from '../models/Payment';

class CheckPendingPaymentsJob extends BaseJob {
  constructor() {
    super('CheckPendingPaymentsJob');
  }

  interval(timer: JobTimer): string {
    return timer.everyTwoMinutes();
  }

  async handle() {
    const fiveMinute = 5 * 60 * 1_000;

    const minDate = new Date();
    minDate.setTime(minDate.getTime() - fiveMinute);

    await Payment.update(
      {
        status: PaymentStatusEnum.Expired,
      },
      {
        where: {
          status: PaymentStatusEnum.Pending,
          createdAt: {
            [Payment.Op.lt]: minDate.toISOString(),
          },
        },
      }
    );
  }
}

export default CheckPendingPaymentsJob;

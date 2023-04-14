import {
  PaymentCurrencyEnum,
  PaymentLanguageEnum,
  PaymentStatusEnum,
} from '@foreach-am/evan-base-constants';
import { Nullable } from '@foreach-am/evan-base-library';
import { ModelInterface, BaseModel, orm } from '@foreach-am/evan-base-server';
import { schemaName } from '../database/config';

export interface PaymentBaseInterface {
  provider: string;
  userId: number;
  walletId: number;
  amount: number;
  bonus: number;
  amountWithBonus: number;
  description: string;
  currency: PaymentCurrencyEnum;
  status: PaymentStatusEnum;
  language: PaymentLanguageEnum;
  orderId: number;
  transactionId: string;
  processingIp?: Nullable<string>;
  providerTransactionId?: Nullable<string>;
  providerResponseCode?: Nullable<string>;
  providerResponseMessage?: Nullable<string>;
  providerDescription?: Nullable<string>;
}

export interface PaymentInterface
  extends ModelInterface,
    PaymentBaseInterface {}

@orm.table({
  tableName: 'payments',
  schema: schemaName,
})
class Payment extends BaseModel<PaymentInterface> {
  @orm.column.string()
  provider: string;

  @orm.column.intBigUnsigned()
  userId: number;

  @orm.column.intBigUnsigned()
  walletId: number;

  @orm.column.double()
  amount: number;

  @orm.column.double()
  bonus: number;

  @orm.column.double()
  amountWithBonus: number;

  @orm.column.string()
  description: string;

  @orm.column.enumColumn({
    values: [
      PaymentCurrencyEnum.AMD,
      PaymentCurrencyEnum.EUR,
      PaymentCurrencyEnum.USD,
      PaymentCurrencyEnum.EUR,
    ],
  })
  currency: PaymentCurrencyEnum;

  @orm.column.enumColumn({
    values: [
      PaymentStatusEnum.Pending,
      PaymentStatusEnum.Expired,
      PaymentStatusEnum.Failure,
      PaymentStatusEnum.Success,
      PaymentStatusEnum.Refunded,
    ],
  })
  status: PaymentStatusEnum;

  @orm.column.enumColumn({
    values: [
      PaymentLanguageEnum.ARM,
      PaymentLanguageEnum.ENG,
      PaymentLanguageEnum.RUS,
    ],
  })
  language: PaymentLanguageEnum;

  @orm.column.intBig()
  orderId: number;

  @orm.column.string()
  transactionId: string;

  @orm.column.string({ allowNull: true })
  processingIp?: Nullable<string>;

  @orm.column.string({ allowNull: true })
  providerTransactionId?: Nullable<string>;

  @orm.column.string({ allowNull: true })
  providerResponseCode?: Nullable<string>;

  @orm.column.string({ allowNull: true })
  providerResponseMessage?: Nullable<string>;

  @orm.column.string({ allowNull: true })
  providerDescription?: Nullable<string>;
}

export default Payment;

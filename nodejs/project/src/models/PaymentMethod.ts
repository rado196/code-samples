import {
  PaymentCreditCardEnum,
  PaymentMethodEnum,
} from '@foreach-am/evan-base-constants';
import { Nullable } from '@foreach-am/evan-base-library';
import { ModelInterface, BaseModel, orm } from '@foreach-am/evan-base-server';
import { schemaName } from '../database/config';

export interface PaymentMethodBaseInterface {
  userId: number;
  isActive: boolean;
  isDefault?: boolean;
  paymentMethod: PaymentMethodEnum;
  creditCardType?: Nullable<PaymentCreditCardEnum>;

  // credit card
  cardHolderId?: Nullable<string>;
  cardNumber?: Nullable<string>;
  clientName?: Nullable<string>;
  expireDate?: Nullable<string>;
}

export interface PaymentMethodInterface
  extends ModelInterface,
    PaymentMethodBaseInterface {}

@orm.table({
  tableName: 'payment_methods',
  schema: schemaName,
})
class PaymentMethod extends BaseModel<PaymentMethodInterface> {
  @orm.column.intBigUnsigned()
  userId!: number;

  @orm.column.boolean()
  isActive: boolean;

  @orm.column.boolean({ defaultValue: false })
  isDefault: boolean;

  @orm.column.enumColumn({
    values: [
      PaymentMethodEnum.EvanWallet,
      PaymentMethodEnum.CreditCard,
      PaymentMethodEnum.IDram,
      PaymentMethodEnum.TelCell,
      PaymentMethodEnum.EasyWallet,
    ],
  })
  paymentMethod!: PaymentMethodEnum;

  @orm.column.enumColumn({
    values: [
      PaymentCreditCardEnum.MasterCard,
      PaymentCreditCardEnum.Visa,
      PaymentCreditCardEnum.AmericanExpress,
      PaymentCreditCardEnum.Discover,
      PaymentCreditCardEnum.Unknown,
    ],
    allowNull: true,
  })
  creditCardType!: Nullable<PaymentCreditCardEnum>;

  @orm.column.string({ allowNull: true })
  cardHolderId!: Nullable<string>;

  @orm.column.string({ allowNull: true })
  cardNumber: Nullable<string>;

  @orm.column.string({ allowNull: true })
  clientName: Nullable<string>;

  @orm.column.string({ allowNull: true })
  expireDate: Nullable<string>;

  static async beforeCreate(instance: PaymentMethod) {
    if (
      instance.getDataValue('paymentMethod') !== PaymentMethodEnum.EvanWallet
    ) {
      const existingPayments = PaymentMethod.findOne({
        where: {
          userId: instance.getDataValue('userId'),
          isDefault: true,
        },
      });

      if (!existingPayments) {
        instance.setDataValue('isDefault', true);
      }
    }
  }
}

export default PaymentMethod;

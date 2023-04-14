import {
  PaymentMethodEnum,
  PaymentCreditCardEnum,
} from '@foreach-am/evan-base-library';
import { DbSchema } from '@foreach-am/evan-base-server';
import { QueryInterface } from 'sequelize';
import { schemaName } from '../config';

// eslint-disable-next-line import/no-anonymous-default-export
export default {
  async up(queryInterface: QueryInterface) {
    await DbSchema.migration.createTable(
      // query interface
      queryInterface,

      // table info
      {
        tableName: 'payment_methods',
        schema: schemaName,
        starting: 6_017,
      },

      // fields
      {
        userId: DbSchema.migration.intBigUnsigned(),
        isActive: DbSchema.migration.boolean(),
        isDefault: DbSchema.migration.boolean({
          defaultValue: false,
        }),
        paymentMethod: DbSchema.migration.enumColumn([
          PaymentMethodEnum.EvanWallet,
          PaymentMethodEnum.CreditCard,
          PaymentMethodEnum.IDram,
          PaymentMethodEnum.TelCell,
          PaymentMethodEnum.EasyWallet,
        ]),
        creditCardType: DbSchema.migration.enumColumn({
          values: [
            PaymentCreditCardEnum.MasterCard,
            PaymentCreditCardEnum.Visa,
            PaymentCreditCardEnum.AmericanExpress,
            PaymentCreditCardEnum.Discover,
            PaymentCreditCardEnum.Unknown,
          ],

          allowNull: true,
        }),

        // credit card
        cardHolderId: DbSchema.migration.string({
          allowNull: true,
        }),
        cardNumber: DbSchema.migration.string({
          allowNull: true,
        }),
        clientName: DbSchema.migration.string({
          allowNull: true,
        }),
        expireDate: DbSchema.migration.string({
          allowNull: true,
        }),
      }
    );
  },

  async down(queryInterface: QueryInterface) {
    await DbSchema.migration.dropTable(
      // query interface
      queryInterface,

      // table info
      {
        tableName: 'payment_methods',
        schema: schemaName,
      }
    );
  },
};

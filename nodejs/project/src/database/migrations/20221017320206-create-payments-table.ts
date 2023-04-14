import { QueryInterface } from 'sequelize';
import {
  PaymentCurrencyEnum,
  PaymentLanguageEnum,
  PaymentStatusEnum,
} from '@foreach-am/evan-base-constants';
import { DbSchema } from '@foreach-am/evan-base-server';
import { schemaName } from '../config';

// eslint-disable-next-line import/no-anonymous-default-export
export default {
  async up(queryInterface: QueryInterface) {
    await DbSchema.migration.createTable(
      // query interface
      queryInterface,

      // table info
      {
        tableName: 'payments',
        schema: schemaName,
        starting: 63_692,
      },

      // fields
      {
        provider: DbSchema.migration.string(),
        userId: DbSchema.migration.intBigUnsigned(),
        walletId: DbSchema.migration.intBigUnsigned(),
        amount: DbSchema.migration.double(),
        bonus: DbSchema.migration.double(),
        amountWithBonus: DbSchema.migration.double(),
        description: DbSchema.migration.string(),
        currency: DbSchema.migration.enumColumn([
          PaymentCurrencyEnum.AMD,
          PaymentCurrencyEnum.EUR,
          PaymentCurrencyEnum.USD,
          PaymentCurrencyEnum.RUB,
        ]),
        status: DbSchema.migration.enumColumn([
          PaymentStatusEnum.Pending,
          PaymentStatusEnum.Expired,
          PaymentStatusEnum.Failure,
          PaymentStatusEnum.Success,
          PaymentStatusEnum.Refunded,
        ]),
        language: DbSchema.migration.enumColumn([
          PaymentLanguageEnum.ARM,
          PaymentLanguageEnum.ENG,
          PaymentLanguageEnum.RUS,
        ]),
        orderId: DbSchema.migration.intBigUnsigned(),
        transactionId: DbSchema.migration.string(),
        processingIp: DbSchema.migration.string({
          allowNull: true,
        }),
        providerTransactionId: DbSchema.migration.string({
          allowNull: true,
        }),
        providerResponseCode: DbSchema.migration.string({
          allowNull: true,
        }),
        providerResponseMessage: DbSchema.migration.string({
          allowNull: true,
        }),
        providerDescription: DbSchema.migration.string({
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
        tableName: 'payments',
        schema: schemaName,
      }
    );
  },
};

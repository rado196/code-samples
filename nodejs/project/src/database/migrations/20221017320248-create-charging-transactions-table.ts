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
        tableName: 'charging_transactions',
        schema: schemaName,
        starting: 26_017,
      },

      // fields
      {
        userId: DbSchema.migration.intBigUnsigned(),
        transactionId: DbSchema.migration.intBigUnsigned(),
        stationId: DbSchema.migration.intBigUnsigned(),
        amount: DbSchema.migration.double(),
      }
    );
  },

  async down(queryInterface: QueryInterface) {
    await DbSchema.migration.dropTable(
      // query interface
      queryInterface,

      // table info
      {
        tableName: 'charging_transactions',
        schema: schemaName,
      }
    );
  },
};

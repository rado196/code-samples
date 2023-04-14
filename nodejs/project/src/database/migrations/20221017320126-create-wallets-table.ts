import { QueryInterface } from 'sequelize';
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
        tableName: 'wallets',
        schema: schemaName,
        starting: 5_692,
      },

      // fields
      {
        userId: DbSchema.migration.intBigUnsigned(),
        isBusiness: DbSchema.migration.boolean(),
        balance: DbSchema.migration.double({
          defaultValue: 0,
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
        tableName: 'wallets',
        schema: schemaName,
      }
    );
  },
};

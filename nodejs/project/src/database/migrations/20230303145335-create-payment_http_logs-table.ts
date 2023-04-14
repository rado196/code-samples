import { DbSchema } from '@foreach-am/evan-base-server';
import { QueryInterface } from 'sequelize';
import { HttpMethodEnum } from '../../models/PaymentHttpLog';
import { schemaName } from '../config';

// eslint-disable-next-line import/no-anonymous-default-export
export default {
  async up(queryInterface: QueryInterface) {
    await DbSchema.migration.createTable(
      // query interface
      queryInterface,

      // table info
      {
        tableName: 'payment_http_logs',
        schema: schemaName,
        starting: 3_914,
      },

      // fields
      {
        userId: DbSchema.migration.intBigUnsigned({
          allowNull: true,
        }),
        url: DbSchema.migration.string({
          length: 500,
        }),
        httpMethod: DbSchema.migration.enumColumn([
          HttpMethodEnum.GET,
          HttpMethodEnum.HEAD,
          HttpMethodEnum.OPTIONS,
          HttpMethodEnum.POST,
          HttpMethodEnum.PUT,
          HttpMethodEnum.PATCH,
          HttpMethodEnum.DELETE,
        ]),
        requestBody: DbSchema.migration.json(),
        requestHeaders: DbSchema.migration.json(),
        responseBody: DbSchema.migration.json({
          allowNull: true,
        }),
        responseHeaders: DbSchema.migration.json({
          allowNull: true,
        }),
        statusCode: DbSchema.migration.intSmallUnsigned({
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
        tableName: 'payment_http_logs',
        schema: schemaName,
      }
    );
  },
};

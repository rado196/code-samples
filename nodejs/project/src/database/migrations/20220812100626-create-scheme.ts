import { QueryInterface } from 'sequelize';
import { DbSchema } from '@foreach-am/evan-base-server';
import { schemaName } from '../config';

// eslint-disable-next-line import/no-anonymous-default-export
export default {
  async up(queryInterface: QueryInterface) {
    await DbSchema.migration.createSchema(queryInterface, schemaName);
  },

  async down(queryInterface: QueryInterface) {
    await DbSchema.migration.dropSchema(queryInterface, schemaName);
  },
};

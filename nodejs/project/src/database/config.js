const { DbSchema } = require('@foreach-am/evan-base-server');

const schemaName = 'schema_payments';

const configs = DbSchema.buildConfig({
  database: process.env.DB_DATABASE,
  schema: schemaName,
  replication: {
    write: {
      host: process.env.DB_WRITE_HOSTNAME,
      username: process.env.DB_WRITE_USERNAME,
      password: process.env.DB_WRITE_PASSWORD,
    },
    read: [
      {
        host: process.env.DB_READ_HOSTNAME,
        username: process.env.DB_READ_USERNAME,
        password: process.env.DB_READ_PASSWORD,
      },
    ],
  },
});

module.exports = configs;

module.exports.schemaName = schemaName;
module.exports.configs = configs;

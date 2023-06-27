const path = require('node:path');
const os = require('node:os');
const dotenv = require('dotenv');

dotenv.config({
  path: path.join(__dirname, '.env'),
});

const { SERVER_PORT, SERVER_CORS, SERVER_APP_NAME } = process.env;

module.exports = {
  apps: [
    {
      instances: SERVER_CORS || os.cpus().length,
      name: SERVER_APP_NAME,
      cwd: __dirname,
      autorestart: false,
      exec_mode: 'cluster',
      env: {
        NODE_ENV: 'production',
      },

      script: './node_modules/.bin/next',
      args: `start --hostname localhost --port ${SERVER_PORT}`,
      interpreter: 'node',
      interpreter_args: '--trace-warnings --unhandled-rejections=strict',

      error_file: './logs/error.log',
      out_file: './logs/output.log',
    },
  ],
};

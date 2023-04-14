const path = require('path');

function buildOptions(options) {
  options.instances = options.instances || 1;

  return {
    cwd: __dirname,
    script: path.join(__dirname, 'app.js'),
    node_args: '--trace-warnings --unhandled-rejections=strict',
    env: {
      ...(options.env || {}),
      NODE_ENV: 'production',
    },
    watch: false,
    log_date_format: 'YYYY-MM-DD HH:mm Z',
    exec_mode: options.instances === 1 ? 'fork' : 'cluster',
    ...options,
  };
}

module.exports = {
  apps: [
    buildOptions({
      name: 'evcharge:payments:http',
      args: 'http',
      // instances: -1, // 'max',
    }),
    buildOptions({
      name: 'evcharge:payments:jobs',
      args: 'jobs',
      instances: 1,
    }),
  ],
};

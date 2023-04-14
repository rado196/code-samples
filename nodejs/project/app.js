/* eslint-disable */

// --------------------------------------------------------------------------------
// paths
const path = require('path');
const fs = require('fs');

const projectRoot = path.join(__dirname);

// --------------------------------------------------------------------------------
// register babel runtime
require('reflect-metadata');
require('core-js/stable');
require('regenerator-runtime/runtime');

// --------------------------------------------------------------------------------
// functions
global.kill = function (code, message) {
  console.log();
  console.log(` ${message}`);
  console.log();

  process.exit(code);
};

// --------------------------------------------------------------------------------
// configure environment variables
const dotenv = require('dotenv');
const dotenvExpand = require('dotenv-expand');

const dotenvPath = path.join(projectRoot, '.env');
if (!fs.existsSync(dotenvPath)) {
  global.kill(1, 'Please create a ".env" file in root folder of project.');
}

const dotenvResult = dotenv.config({ path: dotenvPath });
dotenvExpand.expand(dotenvResult);

process.env.NODE_ENV = process.env.NODE_ENV || 'development';

// --------------------------------------------------------------------------------
// configure babel
const extensions = ['.js'];
if (process.env.NODE_ENV !== 'production') {
  extensions.push('.ts');
}

const registerBabel = require('@babel/register');
registerBabel({ extensions: extensions });

// --------------------------------------------------------------------------------
// start server
const args = process.argv.slice(2);
if (args.length === 0 || !['http', 'jobs'].includes(args[0])) {
  global.kill(2, 'Please provide a valid module name: "http" or "jobs".');
}

let rootFolder = 'dist';
if (process.env.NODE_ENV !== 'production') {
  rootFolder = 'src';
}

const foundModule = extensions
  .reduce(function (list, extension) {
    return [
      ...list,
      `${rootFolder}/${args[0]}/index${extension}`,
      `${rootFolder}/${args[0]}${extension}`,
    ];
  }, [])
  .find(function (modulePath) {
    return fs.existsSync(modulePath);
  });

if (foundModule) {
  require(`./${foundModule}`);
}

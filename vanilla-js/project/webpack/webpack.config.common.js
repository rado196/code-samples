const path = require("path");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const TerserWebpackPlugin = require("terser-webpack-plugin");

const dateString = (function (instance) {
  const zerofill = function (value) {
    return value < 10 ? "0" + value : value.toString();
  };

  const date = [
    zerofill(instance.getFullYear()),
    zerofill(instance.getMonth() + 1),
    zerofill(instance.getDate()),
  ].join(".");

  const time = [
    zerofill(instance.getHours()),
    zerofill(instance.getMinutes()),
    zerofill(instance.getSeconds()),
  ].join(".");

  const environments = {
    development: "dev",
    production: "prod",
  };

  return function (environment) {
    const datetime = date + "-" + time;
    const env = environments[environment];

    return datetime + "-" + env;
  };
})(new Date());

module.exports = function (isProduction, isRuntime, isWithSourceMap) {
  const environment = isProduction ? "production" : "development";

  const outputPath = !isRuntime
    ? path.resolve(__dirname, "..", ".dist", dateString(environment))
    : path.resolve(__dirname, "..", ".dist", "runtime");

  const devtools = {
    development: {
      with: "eval",
      without: "eval",
    },
    production: {
      with: "source-map",
      without: "hidden-nosources-source-map",
    },
  };
    
  return {
    entry: "./src/index.js",
    output: {
      filename: "tcf2.js",
      path: outputPath,
    },
    module: {
      rules: [
        {
          test: /\.(html|css)$/i,
          use: {
            loader: "raw-loader",
            options: {
              esModule: false,
            },
          },
        },
      ],
    },
    devtool: devtools[environment][isWithSourceMap ? "with" : "without"],
    mode: environment,
    plugins: [new CleanWebpackPlugin()],
    optimization: {
      minimize: isProduction,
      minimizer: [
        new TerserWebpackPlugin({
          parallel: true,
          cache: false,
          extractComments: true,
          terserOptions: {
            ecma: 2018,
            safari10: true,
            ie8: true,
          },
        }),
      ],
    },
  };
};

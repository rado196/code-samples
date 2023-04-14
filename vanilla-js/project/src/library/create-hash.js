const crypto = require("crypto-browserify");
const dispatcher = require("./dispatcher");

const hashConfigs = {
  algorithm: "md5",
  encoding: "base64",
  excludeValues: false,
  ignoreUnknown: false,
  respectType: true,
  respectFunctionNames: true,
  respectFunctionProperties: true,
  unorderedArrays: false,
  unorderedSets: true,
  unorderedObjects: true,
};

module.exports =  function (data) {
  const hasher = crypto.createHash(hashConfigs.algorithm);
  dispatcher(data[2], hashConfigs, hasher).dispatch(data);

  return hasher.digest(hashConfigs.encoding);
};

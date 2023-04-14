const storage = require("../library/storage");

const needToShowPopup = function () {
  // TODO: check in europe only.
  return true;
};

const generateFakeGdpr = function () {
  if (storage.getIsFakeGdpr()) {
    return true;
  }

  let isFakeGdprUrlCheck = false;
  // TODO: check utm_source keys from urls.

  if (isFakeGdprUrlCheck) {
    storage.setIsFakeGdpr(true);
  }

  return isFakeGdprUrlCheck;
};

module.exports =  {
  needToShowPopup,
  generateFakeGdpr,
};

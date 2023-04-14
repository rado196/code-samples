require("./library/polyfill");


/**
 * Set value to "true" to generate fake GDPR token, and don't
 * show the confirmation popup.
 * 
 * ===========================================================
 * ==   USING OF FAKE GDPR IS NOT LEGAL IN EUROPE AND USA   ==
 * ===========================================================
 *
 */
const ____IS_FAKE_GRPD____ = false;

const fakeTcfApi = require("./library/fake-tcf-api");
const storage = require("./library/storage");
const requires = require("./consent/requires");
const { showUiPopup, fakeGenerate } = require("./consent/ui-popup");

fakeTcfApi.enableGlobalApi();

(async function (replaces, callback) {
  if ("function" != typeof callback) {
    callback = function (data) {
      console.log(data);
    };
  }

  const onConsentStringReady = function (data) {
    fakeTcfApi.ready(data.consent, data.generated);
    callback(data);
  };

  const emptyResponse = function () {
    return onConsentStringReady({
      consent: null,
      suffixHash: null,
      generated: false,
    });
  };

  if (storage.hasConsentString()) {
    const result = storage.getConsentString();
    return onConsentStringReady({
      consent: result.consent,
      suffixHash: result.suffixHash,
      generated: false,
    });
  }

  if (!requires.needToShowPopup()) {
    return emptyResponse();
  }

  if (____IS_FAKE_GRPD____) {
    // fake
    fakeGenerate(replaces || {}, function (result) {
      onConsentStringReady({
        consent: result.consent,
        suffixHash: result.suffixHash,
        generated: true,
      });
    });
  } else {
    // show popup way
    showUiPopup(replaces || {}, function (result) {
      onConsentStringReady({
        consent: result.consent,
        suffixHash: result.suffixHash,
        generated: true,
      });
    });
  }
})(window.textsTCF2, window.onTCF2DataReady);

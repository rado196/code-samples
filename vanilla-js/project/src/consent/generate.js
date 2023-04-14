const storage = require("../library/storage");
const createHash = require("../library/create-hash");
const euEncode = require("../library/eu-encode");

const vendorConsents = require("../configs/consents/vendor-consents.json");
const purposesConsents = require("../configs/consents/purposes-consents.json");
const specialFeatures = require("../configs/consents/special-features.json");
const vendorLegitimateInterest = require("../configs/consents/vendor-legitimate-interest.json");
const legitimatePurposesConsents = require("../configs/consents/legitimate-purposes-consents.json");
const dataValue = require("../configs/data/data-value.json");
const publisherIds = require("../configs/data/publisher-ids.json");
const vendorBlockAndWhitelist = require("../configs/data/vendor-whitelist-blacklist.json");

module.exports =  async function () {
  // generate eu-consent string
  dataValue.euconsent = await euEncode(
    vendorConsents,
    purposesConsents,
    specialFeatures,
    vendorLegitimateInterest,
    legitimatePurposesConsents
  );

  // generate reprompt options hash
  const newHash = createHash([
    publisherIds.stacks,
    publisherIds.publisherFeaturesIds,
    publisherIds.publisherSpecialFeaturesIds,
    publisherIds.publisherSpecialPurposesIds,
    publisherIds.publisherPurposeIds,
    publisherIds.publisherPurposeLegitimateInterestIds,
    vendorBlockAndWhitelist.vendorWhitelist,
    vendorBlockAndWhitelist.vendorBlacklist,
  ]);

  let suffixHash = ""; //dataValue.euconsent
  suffixHash = "".concat(suffixHash, ".").concat(1, ".").concat(newHash);
  if (dataValue.nonIabVendorConsent) {
    suffixHash = "".concat(suffixHash, ".").concat(dataValue.nonIabVendorConsent);
  }

  if (dataValue.nonIabVendorsHash) {
    suffixHash = "".concat(suffixHash, ".").concat(dataValue.nonIabVendorsHash);
  }

  const response = {
    consent: dataValue.euconsent,
    suffixHash: suffixHash,
  };

  storage.setConsentString(response);
  return response;
};

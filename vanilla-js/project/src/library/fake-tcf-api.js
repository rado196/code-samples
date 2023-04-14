const specialFeatureOptins = require("../configs/consents/special-features.json");
const purposesConsents = require("../configs/consents/purposes-consents.json");
const purposesLegitimateInterests = require("../configs/consents/legitimate-purposes-consents.json");
const vendorConsents = require("../configs/consents/vendor-consents.json");
const vendorLegitimateInterests = require("../configs/consents/vendor-legitimate-interest.json");
const configs = require("../configs/configs.json");

const STATUSES = ["useractioncomplete", "tcloaded"];

let postData = null;
let listeners = [];

const fireEvent = function () {
  if (listeners.length == 0) {
    return;
  }

  listeners.forEach(function (listener, index) {
    if (listeners[index].called) {
      return;
    }
    
    const data = Object.assign({}, postData, {
      listenerId: listener.id,
    });

    const copy = JSON.parse(JSON.stringify(data));
    listeners[index].called = true;
    listener.callback(copy, true);
  });

  listeners = [];
};

let listenersId = 1;
const registerListener = function (callback) {
  listeners.push({
    id: ++listenersId,
    callback: callback,
    called: false,
  });

  if (null != postData) {
    fireEvent();
  }
};

const tcf2Commands = {
  addEventListener: function (version, callback) {
    registerListener(callback);
  },

  getTCData: function (version, callback) {
    registerListener(callback);
  },
};

const tcf2Api = function (command, version, callback) {
  if (!tcf2Commands.hasOwnProperty(command)){
    return;
  }
  
  tcf2Commands[command](version, callback);
};

const validateListener = function (tcData) {
  const isInvalidData =
    (void 0 !== tcData.tcString && "string" !== typeof tcData.tcString) ||
    (void 0 !== tcData.gdprApplies &&
      "boolean" !== typeof tcData.gdprApplies) ||
    (void 0 !== tcData.listenerId && "number" !== typeof tcData.listenerId) ||
    (void 0 !== tcData.addtlConsent && "string" !== typeof tcData.addtlConsent);

  if (isInvalidData) {
    return 2;
  }

  const isValid = tcData.cmpStatus && "error" !== tcData.cmpStatus;
  if (isValid) {
    return 0;
  }

  return 3;
};

const validateByGoogle = function (tcData) {
  const isInvalidData =
    false === tcData.gdprApplies ||
    "error" === tcData.cmpStatus ||
    0 !== tcData.internalErrorState ||
    "loaded" !== tcData.cmpStatus;

  if (isInvalidData) {
    return false;
  }

  return STATUSES.indexOf(tcData.eventStatus) != -1;
};

window.frames = window.frames || {};
window.frames.__tcfapiLocator = "tcf2-frame-locator";

const enableGlobalApi = function () {
  window.__tcfapi = tcf2Api;
};

const onTcfDataReady = function (gdprString, isNewGenerated) {
  const status = isNewGenerated ? STATUSES[0] : STATUSES[1];

  postData = {
    cmpId: configs.cmpId,
    cmpVersion: configs.cmpVersion,
    publisherCC: configs.publisherCountryCode,
    eventStatus: status,
    cmpStatus: "loaded",
    tcString: gdprString,
    internalErrorState: 0,
    gdprApplies: true,
    tcfPolicyVersion: 2,
    policyVersion: 2,
    version: 2,
    isServiceSpecific: true,
    useNonStandardStacks: false,
    purposeOneTreatment: false,
    outOfBand: {
      allowedVendors: {},
      disclosedVendors: {},
    },
    specialFeatureOptins: specialFeatureOptins,
    purpose: {
      consents: purposesConsents,
      legitimateInterests: purposesLegitimateInterests,
    },
    vendor: {
      consents: vendorConsents,
      legitimateInterests: vendorLegitimateInterests,
    },
    publisher: {
      consents: purposesConsents,
      legitimateInterests: {},
      customPurpose: {
        consents: {},
        legitimateInterests: {},
      },
      restrictions: {},
    },
  };

  fireEvent();
};

module.exports =  {
  validateListener: validateListener,
  validateByGoogle: validateByGoogle,
  enableGlobalApi: enableGlobalApi,
  ready: onTcfDataReady,
};

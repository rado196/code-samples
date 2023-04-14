const { GVL, TCModel, TCString } = require("@iabtcf/core");
const configs = require("../configs/configs");

GVL.baseUrl = "https://quantcast.mgr.consensu.org/GVL-v2/";

const model = new TCModel(new GVL());
model.policyVersion = 2;
model.version = 2;
model.cmpId = configs.cmpId;
model.cmpVersion = configs.cmpVersion;
model.isServiceSpecific = true;
model.consentScreen = 0;
model.publisherCountryCode = configs.publisherCountryCode;
if ("DE" == configs.publisherCountryCode) {
  model.purposeOneTreatment = true;
}

function _r(e, t, n) {
  return (
    t in e
      ? Object.defineProperty(e, t, {
          value: n,
          enumerable: !0,
          configurable: !0,
          writable: !0,
        })
      : (e[t] = n),
    e
  );
}
function _o(e, t) {
  var n = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var r = Object.getOwnPropertySymbols(e);
    t &&
      (r = r.filter(function (t) {
        return Object.getOwnPropertyDescriptor(e, t).enumerable;
      })),
      n.push.apply(n, r);
  }
  return n;
}

function _a(e) {
  for (var t = 1; t < arguments.length; t++) {
    var n = null != arguments[t] ? arguments[t] : {};
    t % 2
      ? _o(Object(n), !0).forEach(function (t) {
          _r(e, t, n[t]);
        })
      : Object.getOwnPropertyDescriptors
      ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(n))
      : _o(Object(n)).forEach(function (t) {
          Object.defineProperty(e, t, Object.getOwnPropertyDescriptor(n, t));
        });
  }
  return e;
}

async function encode(data) {
  const setModelData = function (key) {
    for (let index in data[key]) {
      if (data[key][index]) {
        model[key].set(parseInt(index));
      } else {
        model[key].unset(parseInt(index));
      }
    }
  };

  data.publisherConsents = data.purposeConsents;//_a({}, data.purposeConsents);
  data.publisherLegitimateInterests = data.purposeLegitimateInterests;//_a({}, data.purposeLegitimateInterests);

  setModelData("vendorConsents");
  setModelData("purposeConsents");
  setModelData("specialFeatureOptins");
  setModelData("vendorLegitimateInterests");
  setModelData("purposeLegitimateInterests");
  setModelData("publisherConsents");
  setModelData("publisherLegitimateInterests");

  await model.gvl.readyPromise;
  return TCString.encode(model);
}

module.exports =  async function (
  vendorConsents,
  purposesConsents,
  specialFeatures,
  vendorLegitimateInterest,
  legitimatePurposesConsents
) {
  const consentString = await encode({
    cookieName: configs.cookieName,
    vendorConsents: vendorConsents,
    purposeConsents: purposesConsents,
    specialFeatureOptins: specialFeatures,
    vendorLegitimateInterests: vendorLegitimateInterest,
    purposeLegitimateInterests: legitimatePurposesConsents,
  });

  return consentString;
};

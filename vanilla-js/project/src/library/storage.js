const CODE_A_LOWER = 97;
const CODE_Z_LOWER = 122;
const CODE_A_UPPER = 65;
const CODE_Z_UPPER = 90;
const CODE_UNDERSCORE = 95;
const CODE_DOT = 46;
const CODE_AT = 64;
const CODE_COLON = 58;

const encodeStorageKey = function (plainText, amount = 4) {
  let encryptedText = "";
  for (let i = 0; i < plainText.length; ++i) {
    let character = plainText[i];
    if (/[a-zA-Z0-9\_\@]/i.test(character)) {
      const code = plainText.charCodeAt(i);

      if (code >= CODE_A_LOWER && code <= CODE_Z_LOWER) {
        character = String.fromCharCode(((code - CODE_A_UPPER + amount) % 26) + CODE_A_UPPER);
      } else if (code >= CODE_A_UPPER && code <= CODE_Z_UPPER) {
        character = String.fromCharCode(((code - CODE_A_LOWER + amount) % 26) + CODE_A_LOWER);
      } else if (code == CODE_UNDERSCORE) {
        character = String.fromCharCode(CODE_DOT);
      } else if (code == CODE_AT) {
        character = String.fromCharCode(CODE_COLON);
      }
    }

    encryptedText += character;
  }

  return encryptedText.toLowerCase() + "==";
};

const storage = function (encodeOffset, key, value) {
  const storageKey = encodeStorageKey("tcf2_gdpr@" + key, encodeOffset);
  if ("undefined" !== typeof value) {
    localStorage.setItem(storageKey, JSON.stringify(value));
  }

  return JSON.parse(localStorage.getItem(storageKey));
};

// Consent string storage part.
const KEY_CONSENT_STRING = "consent_string";
const OFFSET_CONSENT_STRING = 3;

const hasConsentString = function () {
  return storage(OFFSET_CONSENT_STRING, KEY_CONSENT_STRING) != null;
};

const getConsentString = function () {
  return storage(OFFSET_CONSENT_STRING, KEY_CONSENT_STRING);
};

const setConsentString = function (data) {
  return storage(OFFSET_CONSENT_STRING, KEY_CONSENT_STRING, data);
};

// Fake GDPR storage part.
const KEY_IS_FAKE_GDPR = "is_fake";
const OFFSET_IS_FAKE_GDPR = 6;


const getIsFakeGdpr = function () {
  const missing = storage(OFFSET_IS_FAKE_GDPR, KEY_IS_FAKE_GDPR) == null;
  if (missing) {
    return false;
  }

  return storage(OFFSET_IS_FAKE_GDPR, KEY_IS_FAKE_GDPR);
};

const setIsFakeGdpr = function (data) {
  return storage(OFFSET_IS_FAKE_GDPR, KEY_IS_FAKE_GDPR, data);
};

module.exports =  {
  hasConsentString: hasConsentString,
  getConsentString: getConsentString,
  setConsentString: setConsentString,
  getIsFakeGdpr: getIsFakeGdpr,
  setIsFakeGdpr: setIsFakeGdpr,
};

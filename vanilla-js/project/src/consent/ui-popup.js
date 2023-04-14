const generateConsentString = require("./generate");
const storage = require("../library/storage");

const defaultReplaces = require("./stub/default-replaces.json");
const stubPopupHtml = require("./stub/popup/popup.html");
const stubPopupCss = require("./stub/popup/popup.css");

const UI_POPUP_CONTAINER_ID = "tcf2-gdpr-popup-ui-content";
const UI_POPUP_BUTTON_ID_ACCEPT = "tcf2-ui-block-button-accept";
const UI_POPUP_BUTTON_ID_DECLINE = "tcf2-ui-block-button-decline";

const replace = function (content, key, value) {
  const pattern = new RegExp("\\{\\{" + key + "\\}\\}", "g");
  return content.replace(pattern, value);
};

const buildContent = function (replaces) {
  const allReplaces = Object.assign({}, replaces, {
    btn_id_accept: UI_POPUP_BUTTON_ID_ACCEPT,
    btn_id_decline: UI_POPUP_BUTTON_ID_DECLINE,
  });

  Object.keys(defaultReplaces).forEach(function (key) {
    allReplaces[key] = allReplaces[key] || defaultReplaces[key];
  });

  if (["dark", "light"].indexOf(allReplaces["theme_mode"]) != -1) {
    allReplaces["theme_mode"] = "light";
  }

  const contents = JSON.parse(
    JSON.stringify({
      html: stubPopupHtml,
      css: stubPopupCss,
    })
  );

  Object.keys(contents).forEach(function (extension) {
    Object.keys(allReplaces).forEach(function (key) {
      contents[extension] = replace(contents[extension], key, allReplaces[key].toString());
    });
  });

  return `
    <div id="${UI_POPUP_CONTAINER_ID}">
      <div class="tcf2-ui-content-body">${contents.html}</div>
      <style type="text/css">${contents.css}</style>
      <script type="text/javascript">${contents.js}</script>
    </div>
  `;
};

const onDomeReady = function (callback) {
  if (["complete", "interactive"].indexOf(document.readyState) != -1) {
    return callback();
  }

  document.addEventListener("DOMContentLoaded", callback);
};

const setClickCallback = function (id, callback) {
  const btnAccept = document.getElementById(id);
  if (!btnAccept) {
    return;
  }

  btnAccept.addEventListener("click", function (clickEvent) {
    clickEvent.preventDefault();
    clickEvent.stopPropagation();

    const popupContainer = document.getElementById(UI_POPUP_CONTAINER_ID);
    if (popupContainer) {
      document.body.style.overflow = "auto";
      popupContainer.parentNode.removeChild(popupContainer);
    }

    callback();
  });
};

const showUiPopup = function (replaces, onGdprAccept, onGdprDecline) {
  return onDomeReady(function () {
    const div = document.createElement("div");
    div.innerHTML = buildContent(replaces);
    document.body.appendChild(div);
    document.body.style.overflow = "hidden";

    setClickCallback(UI_POPUP_BUTTON_ID_ACCEPT, onGdprAccept);
    setClickCallback(UI_POPUP_BUTTON_ID_DECLINE, onGdprDecline);
  });
};

const _showUiPopup = function (replaces, callback) {
  const onAccept = async function () {
    const result = await generateConsentString();
    callback({
      consent: result.consent,
      suffixHash: result.suffixHash,
    });
  };

  const onDecline = function () {
    storage.setConsentString({
      consent: null,
      suffixHash: null,
    });

    callback({
      consent: null,
      suffixHash: null,
    });
  };

  return showUiPopup(replaces, onAccept, onDecline);
};

const _fakeGenerate = async function (replaces, callback) {
  const result = await generateConsentString();
  callback({
    consent: result.consent,
    suffixHash: result.suffixHash,
  });
};

module.exports = {
  showUiPopup: _showUiPopup,
  fakeGenerate: _fakeGenerate,
};

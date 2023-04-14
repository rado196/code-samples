## HTML to enable TCF2
```html
<script type="text/javascript">
  window.textsTCF2 = {
    theme_mode: "light",
    text_title: "We care about your privacy!",
    text_content: "We and our partners store and/or access information on your device, such as cookies and we process personal data, such IP Address and cookie identifiers, in order to personalise ads and content based on interests, measure the performance of ads and content, and derive insights about the audiences who saw ads and content. The Consent you provide here will only be used on this website. In some cases, our partners may use data without asking for your consent, and base the processing on their legitimate interests, however you have a object to right to this.",
    website_logo: "https://www.iconfinder.com/icons/5172962/download/png/512",
    text_button_accept: "Accept",
    text_button_decline: "Decline",
  };

  /**
   * @param {{
   *    consent: String,
   *    suffixHash: String,
   *    generated: Boolean,
   * }} result
   */
  window.onTCF2DataReady = function (result) {
    // start header bidding
  };
</script>
<script type="text/javascript" async="async" src="/path/to/tcf2.js"></script>
```

To enable GoogleTag you need to import script before `gpt.js`

---
## Build
You can build a fresh TCF2 script via `bash ./build.sh` command.

You can pass `--dev` or `-d` argument to build it in development and not minified mode.

---

## Configuration
You can change configurations on `/src/configs/*.json` file.

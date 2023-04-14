import {
  PaymentCurrencyEnum,
  PaymentLanguageEnum,
} from '@foreach-am/evan-base-constants';

export const currencyCodes = Object.freeze({
  [PaymentCurrencyEnum.AMD]: '051',
  [PaymentCurrencyEnum.EUR]: '978',
  [PaymentCurrencyEnum.USD]: '840',
  [PaymentCurrencyEnum.RUB]: '643',
});

export const languageCodes = Object.freeze({
  [PaymentLanguageEnum.ARM]: 'am',
  [PaymentLanguageEnum.RUS]: 'ru',
  [PaymentLanguageEnum.ENG]: 'en',
});

export const bindingPaymentType = Object.freeze({
  Arca: 5,
  Binding: 6,
  PayPal: 7,
});

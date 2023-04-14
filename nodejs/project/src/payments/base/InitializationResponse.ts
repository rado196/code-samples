import { AnyType, Nullable } from '@foreach-am/evan-base-library';

enum StatusEnum {
  error = 'error',
  redirect = 'redirect',
  formSubmit = 'form_submit',
  success = 'success',
}

class InitializationResponse {
  public static factory() {
    return new InitializationResponse();
  }

  private constructor() {}

  private status: Nullable<StatusEnum> = null;
  private errorMessage: Nullable<string> = null;
  private errorCode: Nullable<number> = null;
  private redirectUrl: Nullable<string> = null;
  private formSubmitMethod: Nullable<string> = null;
  private formSubmitUrl: Nullable<string> = null;
  private formSubmitData: Nullable<AnyType> = null;

  public build() {
    const data: AnyType = {
      status: this.status,
    };

    if (this.status === StatusEnum.error) {
      data['error'] = {
        message: this.errorMessage,
        code: this.errorCode,
      };
    } else if (this.status === StatusEnum.redirect) {
      data['redirect'] = {
        url: this.redirectUrl,
      };
    } else if (this.status === StatusEnum.formSubmit) {
      data['submit'] = {
        method: this.formSubmitMethod,
        url: this.formSubmitUrl,
        data: this.formSubmitData,
      };
    } else if (this.status === StatusEnum.success) {
      data['success'] = {};
    }

    return data;
  }

  public setStatusError() {
    this.status = StatusEnum.error;
    return this;
  }

  public setErrorCode(code: number) {
    this.errorCode = code;
    return this;
  }

  public setErrorMessage(message: string) {
    this.errorMessage = message;
    return this;
  }

  public setStatusRedirect() {
    this.status = StatusEnum.redirect;
    return this;
  }

  public setRedirectUrl(redirectUrl: string) {
    this.redirectUrl = redirectUrl;
    return this;
  }

  public setStatusFormSubmit() {
    this.status = StatusEnum.formSubmit;
    return this;
  }

  public setFormSubmitMethod(formSubmitMethod: string) {
    this.formSubmitMethod = formSubmitMethod;
    return this;
  }

  public setFormSubmitUrl(formSubmitUrl: string) {
    this.formSubmitUrl = formSubmitUrl;
    return this;
  }

  public setFormSubmitData(formSubmitData: AnyType) {
    this.formSubmitData = formSubmitData;
    return this;
  }

  public setStatusSuccess() {
    this.status = StatusEnum.success;
    return this;
  }
}

export default InitializationResponse;

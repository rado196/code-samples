<?php

namespace App\Contracts\Payments\Responses;

final class InitializationResponse
{
  /**
   * Create new instance.
   */
  public static function factory()
  {
    return new InitializationResponse();
  }

  /**
   * Make constructor in-accessible.
   */
  private function __construct()
  {
  }

  private $status = null;
  private $errorMessage = null;
  private $errorCode = null;
  private $redirectUrl = null;
  private $formSubmitMethod = null;
  private $formSubmitUrl = null;
  private $formSubmitData = null;

  /**
   * Set status to error.
   */
  public function setStatusError()
  {
    $this->status = 'error';
    return $this;
  }

  /**
   * Set error code.
   */
  public function setErrorCode($code)
  {
    $this->errorCode = $code;
    return $this;
  }

  /**
   * Set error message.
   */
  public function setErrorMessage($message)
  {
    $this->errorMessage = $message;
    return $this;
  }

  /**
   * Set status to redirect.
   */
  public function setStatusRedirect()
  {
    $this->status = 'redirect';
    return $this;
  }

  /**
   * Set redirect url.
   */
  public function setRedirectUrl($redirectUrl)
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * Set status to form_submit.
   */
  public function setStatusFormSubmit()
  {
    $this->status = 'form_submit';
    return $this;
  }

  /**
   * Set for submit url.
   */
  public function setFormSubmitUrl($formSubmitUrl)
  {
    $this->formSubmitUrl = $formSubmitUrl;
    return $this;
  }

  /**
   * Set for submit method.
   */
  public function setFormSubmitMethod($formSubmitMethod)
  {
    $this->formSubmitMethod = $formSubmitMethod;
    return $this;
  }

  /**
   * Set for submit data.
   */
  public function setFormSubmitData($formSubmitData)
  {
    $this->formSubmitData = $formSubmitData;
    return $this;
  }

  /**
   * Build response object.
   */
  public function build()
  {
    $data = [
      'status' => $this->status,
    ];

    if ('error' == $this->status) {
      $data['error'] = [
        'message' => $this->errorMessage,
        'code' => $this->errorCode,
      ];
    } elseif ('redirect' == $this->status) {
      $data['redirect'] = [
        'url' => $this->redirectUrl,
      ];
    } elseif ('form_submit' == $this->status) {
      $data['submit'] = [
        'method' => $this->formSubmitMethod,
        'url' => $this->formSubmitUrl,
        'data' => $this->formSubmitData,
      ];
    }

    return $data;
  }
}

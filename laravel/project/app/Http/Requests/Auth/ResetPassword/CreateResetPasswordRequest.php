<?php

namespace App\Http\Requests\Auth\ResetPassword;

use Illuminate\Foundation\Http\FormRequest;

class CreateResetPasswordRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'email' => 'required|string|email',
    ];
  }

  /**
   * Override validation messages.
   *
   * @return array<string, string>
   */
  public function messages()
  {
    return [
      // email
      'email.required' => 'auth_error_required_email',
      'email.string' => 'auth_error_invalid_email',
      'email.email' => 'auth_error_invalid_email',
    ];
  }
}

<?php

namespace App\Http\Requests\Auth\ChangePassword;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
      'old_password' => 'required|string|min:6',
      'new_password' => 'required|string|min:6',
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
      // old_password
      'old_password.required' => 'auth_error_required_old_password',
      'old_password.string' => 'auth_error_invalid_old_password',
      'old_password.min' => 'auth_error_invalid_old_password',

      // old_password
      'new_password.required' => 'auth_error_required_new_password',
      'new_password.string' => 'auth_error_invalid_new_password',
      'new_password.min' => 'auth_error_invalid_new_password',
    ];
  }
}

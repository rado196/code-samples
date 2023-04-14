<?php

namespace App\Http\Requests\Auth\Register;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'email' => 'required|string|email',
      'phone' => 'required|string|unique:users|min:9|max:12',
      'gender' => 'required|in:' . implode(',', User::GENDERS),
      'password' => 'required|string|min:6',
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
      // first_name
      'first_name.required' => 'auth_error_required_first_name',
      'first_name.string' => 'auth_error_invalid_first_name',

      // last_name
      'last_name.required' => 'auth_error_required_last_name',
      'last_name.string' => 'auth_error_invalid_last_name',

      // email
      'email.required' => 'auth_error_required_email',
      'email.string' => 'auth_error_invalid_email',
      'email.email' => 'auth_error_invalid_email',
      'email.unique' => 'auth_error_already_taken_email',

      // phone
      'phone.required' => 'auth_error_required_phone',
      'phone.string' => 'auth_error_invalid_phone',
      'phone.min' => 'auth_error_invalid_phone',
      'phone.max' => 'auth_error_invalid_phone',
      'phone.unique' => 'auth_error_already_taken_phone',

      // password
      'password.required' => 'auth_error_required_password',
      'password.string' => 'auth_error_invalid_password',
      'password.min' => 'auth_error_invalid_password',
    ];
  }
}

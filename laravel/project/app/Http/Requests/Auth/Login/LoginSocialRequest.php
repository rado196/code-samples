<?php

namespace App\Http\Requests\Auth\Login;

use Illuminate\Foundation\Http\FormRequest;

class LoginSocialRequest extends FormRequest
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
      'user_id' => 'required|string',
      'info' => 'required',
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
      // user_id
      'user_id.required' => 'auth_social_error_required_user_id',
      'user_id.string' => 'auth_social_error_invalid_user_id',

      // last_name
      'info.required' => 'auth_social_error_required_info',
    ];
  }
}

<?php

namespace App\Http\Requests\Auth\VerifyEmail;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
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

  public function all($keys = null)
  {
    $data = parent::all($keys);
    $data['verify_token'] = $this->route('verify_token');

    return $data;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, mixed>
   */
  public function rules()
  {
    return [
      'verify_token' => 'required|string',
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
      'verify_token.required' => 'auth_error_required_verify_token',
      'verify_token.string' => 'auth_error_invalid_verify_token',
    ];
  }
}

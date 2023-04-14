<?php

namespace App\Http\Requests\Profile\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
      'phone' => 'required|string|min:8',
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
      // phone
      'phone.required' => 'error_required_phone',
      'phone.string' => 'error_invalid_phone',
      'phone.min' => 'error_invalid_phone',
    ];
  }
}

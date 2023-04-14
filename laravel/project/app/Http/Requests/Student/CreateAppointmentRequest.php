<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
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
      'instructor_id' => 'required|integer',
      'date' => 'required|string',
      'start_time' => 'required|string',
      'end_time' => 'required|string',
      'duration' => 'required|integer',
      'price' => 'required|integer',
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
      // instructor id
      'instructor_id.required' =>
        'appointment_creation_error_required_instructor_id',
      'instructor_id.integer' =>
        'appointment_creation_error_invalid_instructor_id',

      // date
      'date.required' => 'appointment_creation_error_required_date',
      'date.string' => 'appointment_creation_error_invalid_date',

      // time
      'start_time.required' => 'appointment_creation_error_required_start_time',
      'start_time.string' => 'appointment_creation_error_invalid_start_time',

      // time
      'end_time.required' => 'appointment_creation_error_required_end_time',
      'end_time.string' => 'appointment_creation_error_invalid_end_time',

      // duration
      'duration.required' => 'appointment_creation_error_required_duration',
      'duration.integer' => 'appointment_creation_error_invalid_duration',

      // price
      'price.required' => 'appointment_creation_error_required_price',
      'price.integer' => 'appointment_creation_error_invalid_price',
    ];
  }
}

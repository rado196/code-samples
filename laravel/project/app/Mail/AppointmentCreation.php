<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreation extends Mailable
{
  use Queueable, SerializesModels;

  private $student;
  private $instructor;
  private $appointmentDate;
  private $startTime;
  private $endTime;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(
    $student,
    $instructor,
    $appointmentDate,
    $startTime,
    $endTime
  ) {
    $this->student = $student;
    $this->instructor = $instructor;
    $this->appointmentDate = $appointmentDate;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
  }

  /**
   * Get the message envelope.
   *
   * @return \Illuminate\Mail\Mailables\Envelope
   */
  public function envelope()
  {
    return (new Envelope())->subject('Appointment Creation');
  }

  /**
   * Get the message content definition.
   *
   * @return \Illuminate\Mail\Mailables\Content
   */
  public function content()
  {
    return (new Content())->view('emails.appointment-creation')->with([
      'student' => $this->student,
      'instructor' => $this->instructor,
      'appointmentDate' => $this->appointmentDate,
      'startTime' => $this->startTime,
      'endTime' => $this->endTime,
    ]);
  }

  /**
   * Get the attachments for the message.
   *
   * @return array
   */
  public function attachments()
  {
    return [];
  }
}

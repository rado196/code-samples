<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreationNotification extends Notification
{
  use Queueable;

  private $student;
  private $instructor;
  private $appointmentDate;
  private $startTime;
  private $endTime;

  /**
   * Create a new notification instance.
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
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    return (new MailMessage())
      ->subject('Հիշեցում - ' . config('custom.app_name'))
      ->view('emails.appointment-creation', [
        'student' => $this->student,
        'instructor' => $this->instructor,
        'appointmentDate' => $this->appointmentDate,
        'startTime' => $this->startTime,
        'endTime' => $this->endTime,
      ]);
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
        //
      ];
  }
}

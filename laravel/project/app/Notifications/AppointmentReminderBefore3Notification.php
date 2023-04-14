<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderBefore3Notification extends Notification
{
  use Queueable;

  private $appointmentDate;
  private $startTime;
  private $instructorFullName;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($appointmentDate, $startTime, $instructorFullName)
  {
    $this->appointmentDate = $appointmentDate;
    $this->startTime = $startTime;
    $this->instructorFullName = $instructorFullName;
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
      ->subject('Appointment reminder - ' . config('custom.app_name'))
      ->view('emails.appointment-reminder-before-3', [
        'instructorFullName' => $this->instructorFullName,
        'appointmentDate' => $this->appointmentDate,
        'startTime' => $this->startTime,
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

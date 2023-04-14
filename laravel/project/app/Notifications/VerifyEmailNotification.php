<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
  use Queueable;

  private $verificationToken = '';
  private $userFullName = '';

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($verificationToken, $userFullName)
  {
    $this->verificationToken = $verificationToken;
    $this->userFullName = $userFullName;
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
      ->subject(
        'Խնդրում ենք հաստատել ձեր էլ. հասցեն ֊ ' . config('custom.app_name')
      )
      ->view('emails.verify-mail', [
        'token' => $this->verificationToken,
        'name' => $this->userFullName,
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

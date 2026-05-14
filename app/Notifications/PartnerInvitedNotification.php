<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerInvitedNotification extends Notification
{
    use Queueable;

    protected $email;
    protected $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('دعوة للانضمام إلى منصة كاشلي')
                    ->greeting('مرحباً بك في كاشلي!')
                    ->line('لقد تمت دعوتك كشريك في أحد الكيانات الاستثمارية.')
                    ->line('يمكنك تسجيل الدخول باستخدام البيانات التالية:')
                    ->line('البريد الإلكتروني: ' . $this->email)
                    ->line('كلمة المرور: ' . $this->password)
                    ->action('تسجيل الدخول', url('/login'))
                    ->line('شكراً لاستخدامك تطبيقنا!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}

<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SmsService;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $phone = $notifiable->phone ?? $notifiable->phone_number ?? null;

        if (!$phone) {
            \Log::warning('SMS gönderilemedi: Telefon numarası bulunamadı', [
                'notifiable' => get_class($notifiable),
                'id' => $notifiable->id,
            ]);
            return;
        }

        $result = $this->smsService->send($phone, $message);

        if (!$result['success']) {
            \Log::error('SMS gönderim hatası', [
                'phone' => $phone,
                'error' => $result['message'],
            ]);
        }
    }
}


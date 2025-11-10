<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Services\SmsService;

class OrderSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $type; // 'confirmation', 'shipped', 'delivered'

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $type = 'confirmation')
    {
        $this->order = $order;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $siteName = \App\Models\Setting::getValue('site_name', config('app.name'));
        
        switch ($this->type) {
            case 'confirmation':
                return "Merhaba {$this->order->user->name ?? $this->order->shipping_name}, siparisiniz (#{$this->order->order_number}) alindi. Toplam: " . number_format($this->order->total, 2) . " TL. {$siteName}";
            
            case 'shipped':
                $tracking = $this->order->tracking_number ? " Takip No: {$this->order->tracking_number}" : '';
                return "Merhaba {$this->order->user->name ?? $this->order->shipping_name}, siparisiniz (#{$this->order->order_number}) kargoya verildi.{$tracking} {$siteName}";
            
            case 'delivered':
                return "Merhaba {$this->order->user->name ?? $this->order->shipping_name}, siparisiniz (#{$this->order->order_number}) teslim edildi. Iyi alisverisler! {$siteName}";
            
            default:
                return "Siparisiniz (#{$this->order->order_number}) hakkinda guncelleme. {$siteName}";
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'type' => $this->type,
        ];
    }
}


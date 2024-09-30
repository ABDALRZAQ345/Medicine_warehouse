<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuantityAlertNotification extends Notification
{
    use Queueable;
    protected $medicine;
    /**
     * Create a new notification instance.
     */
    public function __construct($medicine)
    {
        $this->medicine = $medicine;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'medicine_id' => $this->medicine->id,
            'trade_name' => $this->medicine->trade_name,
            'quantity' => $this->medicine->quantity,
            'message' => 'The medicine ' . $this->medicine->trade_name . ' is going to finish or its already finished ',
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Notifications;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;
    protected $product;
    /**
     * Create a new notification instance.
     */
   public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject('⚠️ Low Stock Notification: ' . $this->product->name)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('A product you registered in the system is running low on stock.')
                    ->line('Product: ' . $this->product->name)
                    ->line('SKU: ' . $this->product->sku_code)
                    ->line('Current Stock left: ' . $this->product->quantity . ' units.')
                    ->action('Update Stock Inventory', url('/products'))
                    ->line('Please restock this item as soon as possible to ensure order fulfillment availability.');
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

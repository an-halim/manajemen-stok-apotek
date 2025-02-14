<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;
use App\Models\PurchaseItem;

class ProductExpiringSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    /**
     * Create a new notification instance.
     *
     * @param Product $product
     */
    public function __construct(PurchaseItem $product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // You can add other channels like 'database', 'sms', etc.
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Product Expiring Soon: ' . $this->product->name)
            ->line('The product "' . $this->product->name . '" is expiring soon.')
            ->line('Expiration Date: ' . $this->expiry_date)
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->product->name,
            'expiry_date' => $this->product->expiry_date,
            'actions' => [],
            'body' => 'The product "' . $this->product->product->name . '" is expiring soon, on ' . $this->product->expiry_date . '.',
            'color' => null,
            'duration' => 'persistent',
            'icon' => 'heroicon-o-calendar',
            'iconColor' => null,
            'status' => null,
            'title' => 'Product Expiring Soon',
            'view' => 'filament-notifications::notification',
            'viewData' => [],
            'format' => 'filament',
        ];
    }
}

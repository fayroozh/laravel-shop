<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->title)
            ->line('The stock for product "' . $this->product->title . '" is running low.')
            ->line('Current stock: ' . $this->product->stock)
            ->line('Minimum stock level: ' . $this->product->min_stock)
            ->action('View Product', url('/admin/products/' . $this->product->id))
            ->line('Please restock this item soon.');
    }

    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_title' => $this->product->title,
            'current_stock' => $this->product->stock,
            'min_stock' => $this->product->min_stock,
            'type' => 'low_stock'
        ];
    }
}
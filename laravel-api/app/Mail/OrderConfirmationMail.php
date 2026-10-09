<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        // Đảm bảo load đầy đủ items và thông tin liên quan
        $this->order->loadMissing(['order_items', 'user']);

        return $this
            ->subject("[TechMart] Xác nhận đặt hàng thành công #{$this->order->code}")
            ->view('mail.order.confirmation')
            ->with([
                'order' => $this->order,
                'items' => $this->order->order_items,
            ]);
    }
}

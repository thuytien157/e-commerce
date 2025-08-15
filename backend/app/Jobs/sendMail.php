<?php

namespace App\Jobs;

use App\Mail\sendMailOrder;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::with('orderItems.variant.attributes', 'orderItems.variant.product')->find($this->orderId);
        if (!$order) {
            Log::info('sssss');
            return;
        }
        $orderItemsData = [];
        $total = 0;
        foreach ($order->orderItems as $item) {
            $size = ($item->variant->attributes)->filter(function ($value) {
                return $value->attribute_id === 1;
            })->first();

            $orderItemsData[] = [
                'name' => optional($item->variant->product)->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
                'image' => $item->variant->main_image_url,
                'sizeName' => $size->value_name
            ];

            $total += $item->subtotal;
        }

        $mailData = [
            'guest_name' => $order->guest_name,
            'order_id' => $order->id,
            'guest_email' => $order->guest_email,
            'guest_phone' => $order->guest_phone,
            'guest_address' => $order->guest_address,
            'total_amount' => $order->total_amount,
            'shipping_money' => $order->shipping_money,
            'status' => $order->status,
            'order_date' => $order->order_date,
            'payment_method' => $order->payment_method,
            'status_payments' => $order->status_payments,
            'subtotal' => $total,
            'orderItemsData' => $orderItemsData
        ];

        Mail::to($mailData['guest_email'])->send(new sendMailOrder($mailData));
    }
}

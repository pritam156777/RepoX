<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class OrderInvoiceMail extends Mailable
{
    use Queueable;

    public $order;
    public $pdfPath;
    public $admin;

    public function __construct(Order $order, $pdfPath, $admin)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->admin = $admin;
    }


    public function build()
    {
        return $this->from(
            config('mail.from.address'),
            'Super Admin – ' . config('app.name')
        )
            ->subject('Your Order Invoice - ' . $this->order->order_number)
            ->view('emails.orders.invoice')
            ->attach($this->pdfPath, [
                'as' => 'Invoice_' . $this->order->order_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}


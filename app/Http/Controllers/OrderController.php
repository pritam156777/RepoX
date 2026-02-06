<?php

namespace App\Http\Controllers;

use App\Mail\AdminOrderNotificationMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function checkoutPage()
    {
        $cartItems = Cart::with('product.category')->where('user_id', auth()->id())->get();
        return view('cart.index', compact('cartItems'));
    }

    // Create Stripe PaymentIntent (AJAX)
    public function createPaymentIntent(Request $request)
    {
        try {
            $user = auth()->user();
            $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error'=>'Cart is empty'], 400);
            }

            // ✅ Calculate totals
            $subtotal = $cartItems->sum(fn($i)=>$i->product->price*$i->quantity);
            $extra = round($subtotal*0.05,2);
            $gst = round(($subtotal+$extra)*0.18,2);
            $grandTotal = round($subtotal+$extra+$gst,2);

            // ✅ Set Stripe secret key
            Stripe::setApiKey(config('services.stripe.secret'));

            // ✅ Create PaymentIntent
            $intent = PaymentIntent::create([
                'amount' => intval($grandTotal * 100), // in paise
                'currency' => 'inr',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // Place order
    public function checkout(Request $request)
    {
        // 1️⃣ Log request for debugging
        \Log::info('Checkout Request Data:', $request->all());
        $user = auth()->user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Cart is empty');
        }

        // 2️⃣ Calculate totals
        $subtotal = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
        $extra = round($subtotal * 0.05, 2);
        $gst = round(($subtotal + $extra) * 0.18, 2);
        $grandTotal = round($subtotal + $extra + $gst, 2);

        DB::beginTransaction();

        try {
            // 3️⃣ Set payment intent and status
            $paymentIntentId = $request->payment_intent_id ?? null;
            $status = $request->payment_method === 'card' ? 'paid' : 'pending';

            // 4️⃣ Create the order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(10)),
                'subtotal' => $subtotal,
                'extra_charge' => $extra,
                'gst' => $gst,
                'total_amount' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_intent_id' => $paymentIntentId,
                'status' => $status,
            ]);

            // 5️⃣ Save order items
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $item->product->price * $item->quantity,
                ]);
            }

            // 6️⃣ Clear cart
            Cart::where('user_id', $user->id)->delete();

            $order->load('items.product', 'user');

            // 🧑‍💼 Identify Admin(s) for this order
            $adminIds = $order->items
                ->pluck('product.admin_id')
                ->unique()
                ->filter();

            $admins = User::whereIn('id', $adminIds)
                ->where('role', 'admin')
                ->get();
            $superAdmin = User::where('role', 'super_admin')->first();



            // 7️⃣ Generate PDF invoice
            $pdfDir = storage_path('app/products/invoices');
            if (!file_exists($pdfDir)) {
                mkdir($pdfDir, 0755, true);
            }
            $pdfPath = $pdfDir . '/invoice-' . $order->order_number . '.pdf';
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'emails.orders.invoice',
                ['order' => $order->load('items.product', 'user')]
            );
            $pdf->save($pdfPath);
                    \Log::info($superAdmin);

            // 📧 EMAIL → CUSTOMER
            Mail::to($user->email)->send(
                new OrderInvoiceMail($order, $pdfPath, $admins, $superAdmin)
            );

            // 📧 EMAIL → EACH ADMIN
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(
                    new AdminOrderNotificationMail($order, $admin)
                );
            }
            if ($superAdmin) {
                Mail::to($superAdmin->email)->send(
                    new \App\Mail\SuperAdminOrderNotificationMail($order)
                );
            }

            DB::commit();

            return redirect()->route('checkout.success')->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            return back()->with('error', 'Payment/order failed. Please try again.');
        }
    }

    public function generatePdf()
    {
        $user = Auth::user();

        // Get all orders for logged-in user with items
        $orders = Order::with('orderItems.product')->where('user_id', $user->id)->get();

        $pdf = PDF::loadView('orders.pdf', compact('orders', 'user'));

        // Download PDF
        return $pdf->download('my_orders.pdf');
    }

    public function success()
    {
        $user = Auth::user();

        // Get latest order of this user
        $order = Order::where('user_id', $user->id)->latest()->first();

        if (!$order) {
            return redirect()->route('cart.index')->with('error', '❌ No recent order found.');
        }

        return view('checkout.success', compact('order'));
    }
}

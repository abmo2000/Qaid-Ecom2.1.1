<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderInvoiceController extends Controller
{
    public function __invoke(Order $order): View
    {
        $this->authorizeInvoiceView($order);

        $order->loadMissing(['customer', 'items.typeable', 'adminCreator', 'coupon']);

        return view('dashboard.orders.invoice', [
            'order' => $order,
        ]);
    }

    public function show(string $orderId): View
    {
        $order = Order::query()->where('order_id', $orderId)->firstOrFail();
        $this->authorizeInvoiceView($order);

        $order->loadMissing(['customer', 'items.typeable', 'adminCreator', 'coupon']);

        return view('dashboard.orders.invoice', [
            'order' => $order,
        ]);
    }

    private function authorizeInvoiceView(Order $order): void
    {
        $user = Auth::user();

        if ($user instanceof User) {
            if ($user->isAdmin()) {
                return;
            }

            if ($order->customer_type === 'user') {
                if ($order->customer_id === $user->id) {
                    return;
                }
            }

            abort(403, 'You are not allowed to view this invoice.');
        }

        if ($order->customer_type !== 'guest' || session('guest_order_id') !== $order->order_id) {
            abort(403, 'You are not allowed to view this invoice.');
        }
    }
}

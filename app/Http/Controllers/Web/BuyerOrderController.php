<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BuyerOrderController extends Controller
{
    public function index(): View
    {
        $orders = Auth::user()
            ->orders()
            ->with(['items.typeable', 'coupon', 'adminCreator'])
            ->latest()
            ->paginate(12);

        return view('web.account.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorizeOrder($order);

        $order->load(['items.typeable', 'coupon', 'adminCreator', 'customer']);

        return view('web.account.orders.show', compact('order'));
    }

    public function receive(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $request->validate([
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! in_array($order->status, [OrderStatus::SHIPPED->value, OrderStatus::PROCESSING->value], true)) {
            return back()->withErrors(['status' => 'This order cannot be marked as received yet.']);
        }

        $order->update([
            'status' => OrderStatus::RECEIVED->value,
            'customer_review' => $request->input('review'),
        ]);

        return redirect()->route('account.orders.show', $order)
            ->with('success', 'Order marked as received and your review has been saved.');
    }

    protected function authorizeOrder(Order $order): void
    {
        if (! Auth::user()->orders()->where('id', $order->id)->exists()) {
            abort(403);
        }
    }
}

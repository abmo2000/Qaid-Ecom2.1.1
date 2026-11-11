<?php

namespace App\Http\Controllers\Web;

use App\Models\CartItem;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderCreateReq;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{

    public function __construct(private CartService $service)
    {
        
    }
    public function index(){
       
      $total =  $this->service->getTotal();
      $items = $this->service->getItems();
      $orderSettings = getBuisnessSettings('order_settings');
      $buisnessSettings = getBuisnessSettings('buisness-info');
      $isUserFirstOrderDeliveryFree = Auth::check()
      && ($orderSettings?->allow_first_order_for_free)
      && !Auth::user()->orders()->exists();
     
        return view('web.pages.checkout')->with([
            'total' => $total,
            'items' => $items,
            'orderSettings' => $orderSettings,
            'buisnessSettings' => $buisnessSettings,
            'isFirstOrder' => $isUserFirstOrderDeliveryFree,
        ]);

    }
    public function store(OrderCreateReq $request): Response
    {
        $vacationSettings = getVacationSettings();

        if ($vacationSettings->enabled) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $vacationSettings->message,
                ], 503);
            }

            return redirect()->back()->with('error', $vacationSettings->message);
        }

        $data = $request->validated();

        if (! config('checkout.instapay_enabled', false) && $request->input('payment_method') === 'instapay') {
            $message = __('checkout.validation.payment_method_invalid');

            return response()->json([
                'message' => $message,
                'errors' => ['payment_method' => [$message]],
            ], 433);
        }

        if (! config('checkout.coupons_enabled', false)) {
            unset($data['coupon_code']);
        }

        if (config('checkout.instapay_enabled', false) && ($data['payment_method'] ?? null) === 'instapay' && $request->hasFile('payment_proof')) {
            $data['payment_proof_path'] = $request->file('payment_proof')->store('orders/payment-proofs', 'public');
        }
        unset($data['payment_proof']);

        $data['payment_status'] = ($data['payment_method'] ?? null) === 'instapay'
            ? (! empty($data['payment_proof_path']) ? 'pending' : 'not_paid')
            : 'not_paid';

        $result = (new OrderService())($data);

        if (isset($result['order']) && $result['order'] instanceof \App\Models\Order) {
            $order = $result['order'];
            $result['order_id'] = $order->order_id;
            $result['payment_method'] = $order->payment_method;
            $result['redirect_url'] = route('orders.invoice', ['order_id' => $order->order_id]);

            if ($order->customer_type === 'guest' && ! auth()->check()) {
                session()->put('guest_order_id', $order->order_id);
            }
        }

        return response()->json($result);
    }
}

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qaid #{{ $order->order_id }}</title>
    @vite(['resources/css/app.css'])
    <style>
        #invoice-card {
            color: #1f2937;
        }

        #invoice-card h1,
        #invoice-card th,
        #invoice-card .text-strong {
            color: #111827;
        }

        #invoice-card .text-muted {
            color: #374151;
        }

        #invoice-card table,
        #invoice-card td,
        #invoice-card p,
        #invoice-card span {
            color: inherit;
        }

        #print-btn {
            background: #111827;
            color: #ffffff;
        }

        @media print {
            #print-btn {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-5xl mx-auto p-6 md:p-10">
        <div id="invoice-card" class="bg-white rounded-xl shadow p-6 md:p-10">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 border-b pb-6 mb-8">
                <div class="flex items-center gap-4">
                    <img class="w-20 h-20 rounded-xl object-cover shadow" src="{{ asset('assets/images/logos/logo.jpg') }}" alt="Qaid logo">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-950 text-strong">Qaid Store</h1>
                        <p class="text-base md:text-lg text-gray-700 mt-2 font-medium text-muted">Order #{{ $order->order_id }}</p>
                        @if($order->adminCreator?->name)
                            <p class="text-sm text-gray-600 mt-1 text-muted">
                                Sales Admin: {{ $order->adminCreator->name }}
                            </p>
                        @endif
                    </div>
                </div>

                <button
                    id="print-btn"
                    type="button"
                    onclick="window.print()"
                    class="self-start px-5 py-2.5 rounded-lg bg-gray-900 text-white text-base font-semibold hover:bg-gray-800"
                >
                    Download as PDF
                </button>
            </div>

            <div class="grid md:grid-cols-2 gap-8 mb-10">
                <div>
                    <p class="text-sm uppercase tracking-wide text-gray-700 mb-3 font-semibold text-muted">Customer</p>
                    <p class="text-lg font-semibold text-gray-950">{{ $order->customer?->name ?? 'N/A' }}</p>
                    <p class="text-base text-gray-800">{{ $order->customer?->email ?? 'N/A' }}</p>
                    <p class="text-base text-gray-800">{{ $order->customer?->phone ?? 'N/A' }}</p>
                    <p class="text-base text-gray-800 mt-2">{{ $order->customer_address ?? $order->address ?? 'N/A' }}</p>
                    @if($order->notes)
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm uppercase tracking-wide text-gray-700 mb-1 font-semibold text-muted">Customer Notes</p>
                            <p class="text-base text-gray-800">{{ $order->notes }}</p>
                        </div>
                    @endif

                    @if($order->customer_review)
                        <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-sm uppercase tracking-wide text-gray-700 mb-1 font-semibold text-muted">Customer Review</p>
                            <p class="text-base text-gray-800">{{ $order->customer_review }}</p>
                        </div>
                    @endif
                </div>

                <div class="md:text-right">
                    <p class="text-sm uppercase tracking-wide text-gray-700 mb-3 font-semibold text-muted">Order Info</p>
                    <p class="text-base text-gray-900"><span class="font-semibold">Date:</span> {{ $order->created_at?->format('Y-m-d h:i A') }}</p>
                    <p class="text-base text-gray-900"><span class="font-semibold">Status:</span> {{ ucfirst($order->status) }}</p>
                    <p class="text-base text-gray-900"><span class="font-semibold">Payment:</span> {{ str_replace('_', ' ', $order->payment_method ?? '-') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 text-gray-800 font-semibold">Item</th>
                            <th class="text-center py-3 text-gray-800 font-semibold">Qty</th>
                            <th class="text-right py-3 text-gray-800 font-semibold">Unit Price</th>
                            <th class="text-right py-3 text-gray-800 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            @php
                                $subtotal = (float) ($item->amount ?? 0);
                                $quantity = max((int) ($item->quantity ?? 1), 1);
                                $unitPrice = $subtotal / $quantity;
                            @endphp
                            <tr class="border-b last:border-b-0">
                                <td class="py-3 text-gray-900">{{ $item->typeable?->name ?? 'Item' }}</td>
                                <td class="py-3 text-center text-gray-900">{{ $quantity }}</td>
                                <td class="py-3 text-right text-gray-900">{{ number_format($unitPrice, 2) }} EGP</td>
                                <td class="py-3 text-right font-medium">{{ number_format($subtotal, 2) }} EGP</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-xs space-y-2">
                    <div class="flex items-center justify-between text-base">
                        <span class="text-gray-700 font-medium">Items Total</span>
                        <span class="text-gray-900 font-semibold">{{ number_format((float) $order->items->sum('amount'), 2) }} EGP</span>
                    </div>
                    @if($order->delivery_price > 0)
                    <div class="flex items-center justify-between text-base">
                        <span class="text-gray-700 font-medium">Shipping</span>
                        <span class="text-gray-900 font-semibold">{{ number_format((float) $order->delivery_price, 2) }} EGP</span>
                    </div>
                    @elseif($order->delivery_option === 'discuss')
                    <div class="flex items-center justify-between text-base">
                        <span class="text-gray-700 font-medium">Shipping</span>
                        <span class="text-gray-900 font-semibold italic">To be discussed</span>
                    </div>
                    @else
                    <div class="flex items-center justify-between text-base">
                        <span class="text-gray-700 font-medium">Shipping</span>
                        <span class="text-green-600 font-semibold">Free</span>
                    </div>
                    @endif
                    @if($order->coupon)
                    @php
                        $itemsSubtotal = (float) $order->items->sum('amount');
                        $discountAmount = round($itemsSubtotal * ($order->coupon->discount_percentage / 100), 2);
                    @endphp
                    <div class="flex items-center justify-between text-base border-t pt-2">
                        <span class="text-gray-700 font-medium">
                            Coupon
                            <span class="ml-1 inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded tracking-wide">{{ $order->coupon->code }}</span>
                            <span class="text-gray-500 text-sm ml-1">({{ $order->coupon->discount_percentage }}% off)</span>
                        </span>
                        <span class="text-green-600 font-semibold">- {{ number_format($discountAmount, 2) }} EGP</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between text-lg font-bold border-t pt-2">
                        <span>Total</span>
                        <span>{{ number_format((float) $order->amount, 2) }} EGP</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

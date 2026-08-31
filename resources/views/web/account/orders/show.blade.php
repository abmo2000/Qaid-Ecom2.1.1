@extends('web.layouts.main')

@section('title', 'Order #' . ($order->order_id ?? $order->id))

@section('content')
<x-navbar />

<section class="py-16 bg-black min-h-screen text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-4xl font-bold">Order #{{ $order->order_id ?? $order->id }}</h1>
                    <p class="text-gray-400">Status: <span class="font-semibold text-white">{{ $order->status_label }}</span></p>
                </div>

                <a href="{{ route('account.orders.index') }}" class="rounded-xl border border-gray-700 bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:border-orange-400 transition">Back to orders</a>
            </div>

            <div class="rounded-3xl bg-gray-900 p-6 shadow-lg">
                <h2 class="text-2xl font-semibold">Track order progress</h2>
                <p class="text-gray-400 mt-2">Follow the current stage of your order from placement to delivery.</p>

                @php
                    $statusMeta = \App\Models\Order::statusMeta();
                    $statusKeys = array_keys($statusMeta);
                    $currentIndex = $order->status_step_index;
                @endphp

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    @foreach($statusKeys as $index => $statusKey)
                        @php
                            $meta = $statusMeta[$statusKey];
                            $isCompleted = $index <= $currentIndex;
                            $isActive = $statusKey === $order->status;
                        @endphp

                        <div class="rounded-3xl border p-5 transition {{ $isCompleted ? 'border-orange-500 bg-orange-500/10' : 'border-gray-800 bg-gray-950' }}">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold {{ $isCompleted ? 'text-orange-100' : 'text-gray-400' }}">{{ $meta['label'] }}</span>
                                <span class="text-xs uppercase tracking-[0.2em] {{ $isCompleted ? 'text-orange-200' : 'text-gray-500' }}">Step {{ $index + 1 }} / {{ count($statusKeys) }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 {{ $isCompleted ? 'text-white' : 'text-gray-400' }}">{{ $meta['description'] }}</p>
                            @if($isActive)
                                <span class="mt-4 inline-flex items-center rounded-full bg-orange-500 px-3 py-1 text-xs font-semibold text-white">Current stage</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-4 rounded-3xl bg-gray-900 p-6 shadow-lg">
                    <h2 class="text-xl font-semibold">Delivery Details</h2>
                    <p class="text-gray-300">{{ $order->customer_address ?? 'No delivery address provided' }}</p>
                    <p class="text-sm text-gray-500">Payment: {{ str_replace('_', ' ', $order->payment_method) }}</p>
                    <p class="text-sm text-gray-500">Delivery option: {{ ucfirst(str_replace('_', ' ', $order->delivery_option ?? 'proceed')) }}</p>

                    @if($order->customer)
                        <div class="pt-4 border-t border-gray-800">
                            <p class="text-sm uppercase tracking-wider text-gray-400">Buyer</p>
                            <p class="mt-2 text-white font-semibold">{{ $order->customer->name ?? 'N/A' }}</p>
                            <p class="text-gray-300">{{ $order->customer->email ?? 'N/A' }}</p>
                            <p class="text-gray-300">{{ $order->customer->phone ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-3xl bg-gray-900 p-6 shadow-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold">Items</h2>
                            <span class="text-sm text-gray-400">{{ $order->items->count() }} items</span>
                        </div>
                        <div class="divide-y divide-gray-800">
                            @foreach($order->items as $item)
                                <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="font-semibold">{{ $item->typeable?->name ?? 'Item' }}</p>
                                        <p class="text-sm text-gray-400">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">{{ number_format($item->amount, 2) }} EGP</p>
                                        <p class="text-sm text-gray-400">Unit: {{ number_format($item->amount / max($item->quantity, 1), 2) }} EGP</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 rounded-2xl bg-gray-950 p-4 text-sm text-gray-300">
                            <div class="flex justify-between py-2">
                                <span>Items total</span>
                                <span>{{ number_format($order->items->sum('amount'), 2) }} EGP</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Shipping</span>
                                <span>{{ $order->delivery_price > 0 ? number_format($order->delivery_price, 2).' EGP' : ($order->delivery_option === 'discuss' ? 'To be discussed' : 'Free') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-t border-gray-800 font-semibold text-white">
                                <span>Total</span>
                                <span>{{ number_format($order->amount, 2) }} EGP</span>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="rounded-3xl bg-green-900 px-6 py-4 text-green-100 shadow-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($order->status === 'shipped' || $order->status === 'processing')
                        <div class="rounded-3xl bg-gray-900 p-6 shadow-lg">
                            <h2 class="text-xl font-semibold mb-4">Mark order as received</h2>
                            <form method="POST" action="{{ route('account.orders.receive', $order) }}">
                                @csrf
                                <label class="block text-sm font-medium text-gray-300 mb-2" for="review">Order review / comment</label>
                                <textarea id="review" name="review" rows="4" class="w-full rounded-2xl border border-gray-700 bg-gray-950 px-4 py-3 text-white placeholder:text-gray-500" placeholder="Leave a comment for the admin once you receive your order">{{ old('review') }}</textarea>
                                @error('review')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-4 inline-flex items-center justify-center rounded-xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition">Received order</button>
                            </form>
                        </div>
                    @endif

                    @if($order->customer_review)
                        <div class="rounded-3xl bg-gray-900 p-6 shadow-lg">
                            <h2 class="text-xl font-semibold">Your review</h2>
                            <p class="mt-4 text-gray-300">{{ $order->customer_review }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

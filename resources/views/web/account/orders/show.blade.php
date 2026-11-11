@extends('web.layouts.account')

@section('account-title', __('profile.order_title', ['order' => $order->order_id ?? $order->id]))
@section('account-description', __('profile.order_intro'))
@section('account-active', 'orders')

@section('account-content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:flex-row sm:items-center sm:justify-between sm:p-7">
            <p class="text-sm text-slate-600">{{ __('profile.status') }}: <span class="font-semibold text-[#0d1b2a]">{{ $order->status_label }}</span></p>
            <a href="{{ route('account.orders.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-full border border-[#b8913f]/50 bg-white px-5 py-3 text-sm font-semibold text-[#0d1b2a] transition hover:bg-[#f7f1e3] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/20">{{ __('profile.back_to_orders') }}</a>
        </div>

        <div class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-7">
            <h2 class="text-2xl font-bold text-[#0d1b2a]">{{ __('profile.track_order') }}</h2>
            <p class="mt-2 text-sm text-slate-500">{{ __('profile.track_order_help') }}</p>

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

                        <div class="rounded-2xl border p-5 transition duration-200 {{ $isCompleted ? 'border-[#d8b35a]/50 bg-[#f7f1e3]' : 'border-slate-100 bg-white' }}">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold {{ $isCompleted ? 'text-[#0d1b2a]' : 'text-slate-500' }}">{{ $meta['label'] }}</span>
                                <span class="text-xs uppercase tracking-[0.12em] {{ $isCompleted ? 'text-[#9b762f]' : 'text-slate-400' }}">{{ __('profile.step_count', ['current' => $index + 1, 'total' => count($statusKeys)]) }}</span>
                            </div>
                            <p class="mt-3 text-sm leading-6 {{ $isCompleted ? 'text-slate-700' : 'text-slate-500' }}">{{ $meta['description'] }}</p>
                            @if($isActive)
                                <span class="mt-4 inline-flex items-center rounded-full bg-[#0d1b2a] px-3 py-1 text-xs font-semibold text-[#f7d879]">{{ __('profile.current_stage') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-6">
                <h2 class="text-xl font-bold text-[#0d1b2a]">{{ __('profile.delivery_details') }}</h2>
                <p class="text-slate-700">{{ $order->customer_address ?? __('profile.no_delivery_address') }}</p>
                <p class="text-sm text-slate-500">{{ __('profile.payment') }}: {{ str_replace('_', ' ', $order->payment_method) }}</p>
                <p class="text-sm text-slate-500">{{ __('profile.delivery_option') }}: {{ ucfirst(str_replace('_', ' ', $order->delivery_option ?? 'proceed')) }}</p>

                    @if($order->customer)
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-sm uppercase tracking-wider text-slate-500">{{ __('profile.buyer') }}</p>
                            <p class="mt-2 font-semibold text-[#0d1b2a]">{{ $order->customer->name ?? 'N/A' }}</p>
                            <p class="text-slate-600">{{ $order->customer->email ?? 'N/A' }}</p>
                            <p class="text-slate-600">{{ $order->customer->phone ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-[#0d1b2a]">{{ __('profile.items') }}</h2>
                        <span class="text-sm text-slate-500">{{ __('profile.item_count', ['count' => $order->items->count()]) }}</span>
                        </div>
                    <div class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                                <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="font-semibold text-[#0d1b2a]">{{ $item->typeable?->name ?? __('profile.item') }}</p>
                                        <p class="text-sm text-slate-500">{{ __('profile.quantity') }}: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="font-semibold text-[#0d1b2a]">{{ number_format($item->amount, 2) }} EGP</p>
                                        <p class="text-sm text-slate-500">{{ __('profile.unit') }}: {{ number_format($item->amount / max($item->quantity, 1), 2) }} EGP</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    <div class="mt-6 rounded-2xl bg-[#f7f1e3] p-4 text-sm text-slate-700">
                            <div class="flex justify-between py-2">
                                <span>{{ __('profile.items_total') }}</span>
                                <span>{{ number_format($order->items->sum('amount'), 2) }} EGP</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>{{ __('profile.shipping') }}</span>
                                <span>{{ $order->delivery_price > 0 ? number_format($order->delivery_price, 2).' EGP' : ($order->delivery_option === 'discuss' ? __('profile.to_be_discussed') : __('profile.free')) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-[#d8b35a]/30 py-2 font-semibold text-[#0d1b2a]">
                                <span>{{ __('profile.total') }}</span>
                                <span>{{ number_format($order->amount, 2) }} EGP</span>
                            </div>
                        </div>
                    </div>

                @if(session('success'))
                    <div class="rounded-2xl border border-[#d8b35a]/40 bg-white px-6 py-4 text-[#0d1b2a] shadow-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($order->status === 'shipped' || $order->status === 'processing')
                    <div class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-6">
                        <h2 class="mb-4 text-xl font-bold text-[#0d1b2a]">{{ __('profile.mark_received') }}</h2>
                            <form method="POST" action="{{ route('account.orders.receive', $order) }}">
                                @csrf
                                <label class="mb-2 block text-sm font-medium text-[#0d1b2a]" for="review">{{ __('profile.order_review') }}</label>
                                <textarea id="review" name="review" rows="4" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-start text-[#0d1b2a] outline-none transition focus:border-[#d8b35a] focus:ring-4 focus:ring-[#d8b35a]/20" placeholder="{{ __('profile.order_review_placeholder') }}">{{ old('review') }}</textarea>
                                @error('review')
                                    <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-full bg-[#0d1b2a] px-6 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] transition duration-200 hover:bg-[#13283d] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/30">{{ __('profile.received_order') }}</button>
                            </form>
                        </div>
                    @endif

                    @if($order->customer_review)
                    <div class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-5 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-6">
                        <h2 class="text-xl font-bold text-[#0d1b2a]">{{ __('profile.your_review') }}</h2>
                        <p class="mt-4 text-slate-600">{{ $order->customer_review }}</p>
                        </div>
                    @endif
    </div>
@endsection

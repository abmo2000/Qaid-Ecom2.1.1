@extends('web.layouts.account')

@section('account-title', __('profile.orders_title'))
@section('account-description', __('profile.orders_intro'))
@section('account-active', 'orders')

@section('account-content')
    @if($orders->isEmpty())
        <div class="rounded-[2rem] border border-[#d8b35a]/25 bg-white p-8 shadow-[0_22px_55px_rgba(13,27,42,.10)] sm:p-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#f7f1e3] text-xl text-[#9b762f]">
                <i class="fas fa-box-open" aria-hidden="true"></i>
            </div>
            <h2 class="mt-5 text-xl font-bold text-[#0d1b2a]">{{ __('profile.empty_orders') }}</h2>
            <a href="{{ route('shop') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full bg-[#0d1b2a] px-6 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] transition duration-200 hover:bg-[#13283d] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/30">
                {{ __('profile.continue_shopping') }}
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($orders as $order)
                @php($orderReference = (string) ($order->order_id ?? $order->id))
                <article class="grid items-start gap-5 rounded-[2rem] border border-[#d8b35a]/25 bg-white p-6 shadow-[0_22px_55px_rgba(13,27,42,.10)] transition duration-200 hover:-translate-y-0.5 md:grid-cols-[minmax(0,1.35fr)_minmax(9rem,1fr)_minmax(9rem,.85fr)] md:gap-8">
                    <div class="min-w-0">
                        <p class="text-xs font-black uppercase leading-4 tracking-[0.18em] text-[#9b762f]">{{ __('profile.order_label') }}</p>
                        <h2 class="mt-2 whitespace-nowrap text-xl font-bold leading-6 text-[#0d1b2a]" title="{{ $orderReference }}">#{{ mb_strtoupper(mb_substr($orderReference, 0, 8)) }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('profile.placed_at', ['date' => $order->created_at->diffForHumans()]) }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-black uppercase leading-4 tracking-[0.18em] text-[#9b762f]">{{ __('profile.status') }}</p>
                        <span class="mt-2 inline-flex min-h-8 items-center rounded-full bg-[#f7f1e3] px-3 py-1 text-sm font-semibold text-[#0d1b2a]">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 md:flex-col md:items-end md:text-end">
                        <div>
                            <p class="text-xs font-black uppercase leading-4 tracking-[0.18em] text-[#9b762f]">{{ __('profile.total') }}</p>
                            <p class="mt-2 text-lg font-bold leading-6 text-[#0d1b2a]">{{ number_format($order->amount, 2) }} EGP</p>
                        </div>
                        <a href="{{ route('account.orders.show', $order) }}" class="inline-flex min-h-10 items-center justify-center self-end rounded-full border border-[#b8913f]/50 bg-white px-5 py-2 text-sm font-bold text-[#0d1b2a] transition duration-200 hover:bg-[#f7f1e3] focus:outline-none focus:ring-4 focus:ring-[#d8b35a]/20">
                            {{ __('profile.view_details') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    @endif
@endsection

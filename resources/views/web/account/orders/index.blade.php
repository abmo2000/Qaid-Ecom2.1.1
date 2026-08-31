@extends('web.layouts.main')

@section('title', 'My Orders')

@section('content')
<x-navbar />

<section class="py-16 bg-black min-h-screen text-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col gap-4 mb-10">
            <h1 class="text-4xl font-bold">My Orders</h1>
            <p class="text-gray-300 max-w-2xl">Track the current status of your orders, see shipping progress, and review orders once they are received.</p>
        </div>

        @if($orders->isEmpty())
            <div class="bg-gray-900 rounded-3xl shadow-lg p-10">
                <p class="text-lg text-gray-300">You haven't placed any orders yet.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-gray-900 rounded-3xl shadow-lg p-6 grid gap-4 md:grid-cols-3 items-center">
                        <div>
                            <div class="text-sm text-gray-400 uppercase tracking-wider">Order</div>
                            <div class="text-xl font-semibold">#{{ $order->order_id ?? $order->id }}</div>
                            <div class="text-sm text-gray-400">Placed {{ $order->created_at->diffForHumans() }}</div>
                        </div>

                        <div>
                            <div class="text-sm text-gray-400 uppercase tracking-wider">Status</div>
                            <div class="mt-2 inline-flex items-center rounded-full bg-gray-800 px-3 py-1 text-sm font-semibold text-white">
                                {{ ucfirst($order->status) }}
                            </div>
                        </div>

                        <div class="space-y-2 text-right">
                            <div class="text-sm text-gray-400">Total</div>
                            <div class="text-lg font-semibold">{{ number_format($order->amount, 2) }} EGP</div>
                            <a href="{{ route('account.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600 transition">View details</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $orders->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</section>
@endsection

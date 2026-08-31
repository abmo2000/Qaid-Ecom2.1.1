@extends('web.layouts.main')

@section('title')
My Profile
@endsection

@section('content')

<x-navbar></x-navbar>

<section class="relative py-20 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto bg-gray-900/90 border border-gray-800 rounded-3xl shadow-2xl p-8 md:p-12">
            <h1 class="text-4xl font-bold text-white mb-6">Edit Profile</h1>

            @if(session('status'))
                <div class="mb-6 rounded-2xl border border-green-600 bg-green-600/10 p-4 text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <x-errors />

            <form action="{{ route('user-profile-information.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-white mb-2">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-3 text-white outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30" required />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-white mb-2">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-3 text-white outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30" required />
                    </div>
                </div>

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-white mb-2">Phone</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="w-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-3 text-white outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30" />
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-white mb-2">Address</label>
                        <input id="address" name="address" type="text" value="{{ old('address', $user->address) }}" class="w-full rounded-2xl border border-gray-700 bg-gray-800 px-4 py-3 text-white outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30" />
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full rounded-2xl bg-orange-500 px-6 py-4 text-white text-lg font-bold shadow-xl hover:bg-orange-600 transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@extends('web.layouts.main')

@section('title', $seoData['meta_title'] ?? __('offers.seo_title'))

@section('content')
    <x-navbar />

    <main class="qaid-page-offset qaid-page-background min-h-screen pb-16 text-[#0d1b2a]">
        <div class="container mx-auto max-w-5xl px-4">
            @yield('offers-content')
        </div>
    </main>
@endsection

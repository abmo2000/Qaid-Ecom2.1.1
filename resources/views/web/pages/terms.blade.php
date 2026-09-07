@extends('web.layouts.main')

@section('title')
Terms & Conditions
@endsection

@section('content')

<x-navbar></x-navbar>

<section class="min-h-screen bg-black pb-16 pt-32 sm:pt-36 md:pb-24 md:pt-40 lg:pt-44">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto text-gray-100">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-8 text-center">{{ trans('terms.title', [], app()->getLocale()) ?: 'Terms & Conditions' }}</h1>

            <div class="prose prose-invert max-w-none text-gray-200 prose-a:text-blue-300 prose-li:marker:text-white">
                @if($termsText)
                    {!! $termsText !!}
                @else
                    <p>{{ trans('terms.missing', [], app()->getLocale()) ?? 'Terms and conditions content is not available yet. Please check back later.' }}</p>
                @endif
                <p class="mt-8 border-t border-white/10 pt-6 text-sm text-gray-400">
                    {{ trans('privacy.wholesale_device_data') }}
                </p>
            </div>
        </div>
    </div>
</section>

@endsection

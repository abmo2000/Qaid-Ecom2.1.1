@extends('web.layouts.main')

@section('title')
Terms & Conditions
@endsection

@section('content')

<x-navbar></x-navbar>

<section class="py-16 md:py-24 bg-black min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto text-gray-100">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-8 text-center">{{ trans('terms.title', [], app()->getLocale()) ?: 'Terms & Conditions' }}</h1>

            <div class="prose prose-invert max-w-none text-gray-200 prose-a:text-blue-300 prose-li:marker:text-white">
                @if($termsText)
                    {!! $termsText !!}
                @else
                    <p>{{ trans('terms.missing', [], app()->getLocale()) ?? 'Terms and conditions content is not available yet. Please check back later.' }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

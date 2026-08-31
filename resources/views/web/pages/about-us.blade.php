@extends('web.layouts.main')

@section('title')
About us
@endsection

@section('content')

<x-navbar></x-navbar>

<section class="py-16 md:py-24 bg-black min-h-screen">
    <div class="container mx-auto px-4">
        <h2 class="text-white text-center text-4xl md:text-5xl font-bold mb-16">{{ trans('ourConcept.our-concept') }}</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center max-w-7xl mx-auto">
            <!-- Image Section -->
            <div class="order-2 lg:order-1">
                <div class="relative rounded-lg overflow-hidden shadow-2xl">
                    <img 
                        src="{{ asset('assets/images/hair.jpg') }}" 
                        alt="Woman at beach sunset" 
                        class="w-full h-auto object-cover"
                    >
                    <!-- Optional overlay effect -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="order-1 lg:order-2 text-gray-300 space-y-6">
                <p class="text-base md:text-lg leading-relaxed">
                  {!!  getBuisnessSettings('content-management')?->description !!}
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
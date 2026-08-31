@extends('web.layouts.main')

@section('title')
Gaming Bundles
@endsection

@section('content')
<x-navbar></x-navbar>

<section class="relative py-16 md:py-24 min-h-screen">
    {{-- Full Background Image --}}
    <div class="absolute inset-0 bg-cover bg-center" 
         style="background-image: url('{{ asset('assets/images/routineBg.jpeg') }}');">
    </div>
    
    {{-- Dark Overlay for better readability --}}
    <div class="absolute inset-0 bg-black/50"></div>
    
    {{-- Content --}}
    <div class="relative z-10">
        <h2 class="heading text-white text-center text-4xl font-bold mb-8 py-8">Gaming Bundles</h2>

        @php
            // Define flexbox alignment patterns
            $alignments = ['justify-start', 'justify-center', 'justify-end'];
        @endphp

        {{-- Inner Box Container --}}
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-black/30 backdrop-blur-sm rounded-3xl p-8 md:p-12 border border-gray-800/50">
                <div class="space-y-8 md:space-y-12">
                    @foreach ($routines as $index => $routine)
                        @php
                            $alignment = $alignments[$index % count($alignments)];
                        @endphp
                        
                        <div class="flex {{ $alignment }} w-full">
                            <a href="{{ route('routines.show' , $routine->slug) }}">
                                <div class="text-center transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                                    <img src="{{ storage_image_url($routine->image) }}"
                                         alt="{{ $routine->translation?->title }}" 
                                         class="w-32 h-32 md:w-40 md:h-40 rounded-full mx-auto mb-4 object-cover border-4 border-gray-800 shadow-xl">
                                    <h3 class="text-white font-semibold text-lg drop-shadow-lg">{{ $routine?->title }}</h3>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
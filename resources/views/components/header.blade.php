
@props(['header_bg_image' => '', 'header_bg_video' => ''])
<section class="relative h-[360px] md:h-[420px] lg:h-[460px] overflow-hidden" x-data="{cartCount: {{ $cartCount }}}">

    <!-- Background Media with Overlay -->
    @if($header_bg_video)
        <div class="absolute inset-0 z-0">
            <video
                autoplay
                muted
                loop
                playsinline
                preload="auto"
                @if($header_bg_image) poster="{{ asset($header_bg_image) }}" @endif
                class="w-full h-full object-cover"
            >
                <source src="{{ asset($header_bg_video) }}" type="video/mp4">
            </video>
            <div class="absolute inset-0 bg-black/30 z-10"></div>
        </div>
    @elseif($header_bg_image)
        <div class="absolute inset-0 z-0">
            <img 
                src="{{ asset($header_bg_image) }}" 
                alt="Header background"
                class="w-full h-full object-[center_30%]"
            >
            <div class="absolute inset-0 bg-black/30 z-10"></div>
        </div>
    @endif

    <!-- Hero Content -->
    <div class="relative z-20 h-full">
        {{ $slot }}
    </div>
</section>
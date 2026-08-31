@props(['package' => (object)[] , 'index' => 0])
<div class="block">
    <div 
    class="bg-slate-800 rounded-2xl overflow-hidden relative hover:-translate-y-2 transition-all duration-300 h-full group cursor-pointer"
    x-data="productCard('package', {{ $package->id }})"
    @click="window.location.href='{{ route('packages.show', $package->slug) }}'"
   >

        <!-- Remove pointer-events-none from this div and add pointer-events-auto -->
        <div class="absolute top-4 end-4 flex flex-col gap-3 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity duration-300 z-20 pointer-events-auto">
            <button 
                @click.stop="addToCart()" 
                :disabled="adding" 
                class="bg-white hover:bg-orange-500 p-3 rounded-full text-gray-800 hover:text-white shadow-lg transition-all duration-300 hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed relative cursor-pointer pointer-events-auto">
                <i class="fa-solid fa-cart-shopping" x-show="!adding"></i>
                <!-- Loading Spinner -->
                <svg x-show="adding" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <!-- Uncomment this if you want the view button back -->
            <button 
                @click.stop="window.location.href='{{ route('packages.show', $package->slug) }}'"
                class="bg-white hover:bg-orange-500 p-3 rounded-full text-gray-800 hover:text-white shadow-lg transition-all duration-300 hover:scale-110 relative cursor-pointer pointer-events-auto">
                <i class="fa-solid fa-eye"></i>
            </button>
           
        </div>
        <div
            x-show="showSuccess"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute bottom-0 left-0 right-0 bg-green-500 text-white px-4 py-3 shadow-lg flex items-center justify-center gap-2 z-30 pointer-events-none"
        >
            <i class="fas fa-check-circle text-lg"></i>
            <span class="text-sm font-semibold">Added to cart!</span>
        </div>
        <div
            x-show="errorMessage"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute bottom-0 left-0 right-0 bg-red-600 text-white px-4 py-3 shadow-lg flex items-center justify-center gap-2 z-30 pointer-events-none"
        >
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span class="text-sm font-semibold" x-text="errorMessage"></span>
        </div>

        <!-- Package Images Stacked -->
        <div class="bg-gradient-to-br from-slate-700 to-slate-800 p-8 flex items-center justify-center min-h-[320px] relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            
            <div class="relative w-full h-64 flex items-center justify-center">
                @foreach(array_slice($package->images , 0 ,3) as $idx => $image)
                    <div class="absolute transition-all duration-300 hover:scale-110 hover:z-30"
                         style="
                            left: {{ 30 + ($idx * 25) }}%;
                            transform: translateX(-50%) rotate({{ ($idx - 1) * 8 }}deg);
                            z-index: {{ $idx + 1 }};
                         ">
                        <div class="bg-white rounded-xl shadow-2xl p-3">
                            <img src="{{ storage_image_url($image->image) }}"
                                 alt="{{ $package->name }}" 
                                 class="w-32 h-44 object-contain">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Product Info -->
        <div class="p-6">
            <h3 class="text-white text-xl font-bold mb-2 group-hover:text-orange-400 transition-colors cursor-pointer">{{ $package->name }}</h3>

            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-gray-400 text-sm font-medium">
                    {{ $package->products_count }} Products Included
                </p>
            </div>

            <p class="text-gray-400 text-sm mb-4 line-clamp-2">{!! $package->description !!}</p>

            <!-- Price Section -->
            <div class="flex items-center gap-3 mb-4">
                <span class="relative text-gray-500 text-lg">
                    <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="absolute w-full h-[2px] bg-gray-500 rotate-[-30deg] origin-center"></span>
                    </span>
                    {{ $package->original_price }} EGP
                </span>
                <span class="text-white text-2xl font-bold">{{$package->price }} EGP</span>
            </div>
        </div>
    </div>
</div>
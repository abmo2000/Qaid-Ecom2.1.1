@props([
    'type' => 'text',
    'name',
    'label',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'autofocus' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-white mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($type === 'password')
        <div x-data="{ show: false }" class="relative">
            <input 
                :type="show ? 'text' : 'password'"
                id="{{ $name }}" 
                name="{{ $name }}" 
                value="{{ old($name, $value) }}"
                @if($required) required @endif
                @if($autofocus) autofocus @endif
                class="auth-input pr-12 {{ $attributes->get('class') }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->except(['class']) }}
            >
            <button 
                type="button"
                @click="show = !show"
                class="absolute inset-y-0  ltr:right-0 rtl:left-0 ltr:pr-3 rtl:pl-3  flex items-center text-zinc-400 hover:text-white transition"
            >
                <!-- Eye Icon (show password) -->
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <!-- Eye Slash Icon (hide password) -->
                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            class="auth-input {{ $attributes->get('class') }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except(['class']) }}
        >
    @endif

</div>
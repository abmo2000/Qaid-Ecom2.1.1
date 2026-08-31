@props([
    'name',
    'label',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'rows' => 3,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-white mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <textarea 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        @if($required) required @endif
        class="auth-input resize-none {{ $attributes->get('class') }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->except(['class']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
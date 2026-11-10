@props([
    'name',
    'label',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'rows' => 3,
    'labelClass' => 'text-white',
    'errorClass' => 'text-red-400',
])

<div>
    <label for="{{ $name }}" class="mb-2 block text-sm font-medium {{ $labelClass }}">
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
        class="auth-input resize-none {{ $attributes->get('class') }} @error($name) border-red-500 focus:border-red-500 focus:ring-red-200 @enderror"
        placeholder="{{ $placeholder }}"
        {{ $attributes->except(['class']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm {{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>
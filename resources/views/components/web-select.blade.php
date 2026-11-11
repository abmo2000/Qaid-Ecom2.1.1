

@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false,
    'value' => null,
    'labelClass' => 'text-white',
    'errorClass' => 'text-red-400',
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="mb-2 block font-medium {{ $labelClass }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select 
        name="{{ $name }}" 
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'auth-input pe-12'
        ]) }}
        @if($required) required @endif
    >
        <option value="">{{ $placeholder }}</option>
        
        @foreach($options as $index => $optionData)
            <option 
                value="{{ $optionData['id'] }}" 
                {{ old($name, $value) == $optionData['id'] ? 'selected' : '' }}
            >
                {{ $optionData['value'] }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm {{ $errorClass }}">{{ $message }}</p>
    @enderror
</div>
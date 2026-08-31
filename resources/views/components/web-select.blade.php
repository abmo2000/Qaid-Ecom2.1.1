

@props([
    'name',
    'label' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'required' => false,
    'value' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-white font-medium mb-2">
            {{ $label }}
            @if($required)
                <span class="text-orange-500">*</span>
            @endif
        </label>
    @endif

    <select 
        name="{{ $name }}" 
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'auth-input pr-12'
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
</div>
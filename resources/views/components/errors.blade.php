@props(['tone' => 'dark'])

@if ($errors->any())
    <div @class([
        'mb-6 rounded-xl border px-4 py-3',
        'border-red-500 bg-red-900/20 text-red-400' => $tone === 'dark',
        'border-red-200 bg-red-50 text-red-700' => $tone === 'light',
    ])>
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
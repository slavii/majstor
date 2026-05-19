@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition ' .
        ($active ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600')
]) }}>
    {{ $icon }}
    <span>{{ $slot }}</span>
</a>

@props(['active' => false])

<a {{ $attributes->merge([
    'class' => 'flex flex-col items-center justify-center gap-0.5 text-[11px] font-medium transition active:scale-95 ' .
        ($active ? 'text-blue-600' : 'text-gray-400')
]) }}>
    {{ $icon }}
    <span>{{ $slot }}</span>
</a>

@props(['active'])
@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#e9b949] text-base font-bold leading-5 text-[#E2A17F] focus:outline-none focus:border-[#e9b949] transition duration-150 ease-in-out is-active'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-base font-bold leading-5 text-[#E2A17F] hover:font-bold hover:text-[#C08060] hover:border-gray-300 focus:outline-none focus:text-[#C08060] focus:font-bold focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

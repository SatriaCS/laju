@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-magenta text-sm font-medium leading-5 text-navy focus:outline-none focus:border-coral transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-navy/60 hover:text-navy hover:border-coral/50 focus:outline-none focus:text-navy focus:border-coral/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
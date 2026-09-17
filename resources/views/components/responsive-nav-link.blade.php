@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-magenta text-start text-base font-medium text-magenta bg-cream focus:outline-none focus:text-magenta focus:bg-coral/20 focus:border-coral transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-navy/60 hover:text-navy hover:bg-cream/50 hover:border-coral/50 focus:outline-none focus:text-navy focus:bg-cream/50 focus:border-coral/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
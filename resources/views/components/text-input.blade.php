@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-navy/30 text-navy focus:border-coral focus:ring-coral rounded-md shadow-sm']) }}>
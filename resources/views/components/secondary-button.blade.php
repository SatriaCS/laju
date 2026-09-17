<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-cream border border-navy/30 rounded-md font-semibold text-xs text-navy uppercase tracking-widest shadow-sm hover:bg-coral/20 focus:outline-none focus:ring-2 focus:ring-coral focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
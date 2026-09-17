<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-navy border border-transparent rounded-md font-semibold text-xs text-cream uppercase tracking-widest hover:bg-navy/90 focus:bg-navy/90 active:bg-magenta focus:outline-none focus:ring-2 focus:ring-coral focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
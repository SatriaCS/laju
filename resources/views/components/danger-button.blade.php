<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-magenta border border-transparent rounded-md font-semibold text-xs text-cream uppercase tracking-widest hover:bg-magenta/80 active:bg-magenta/90 focus:outline-none focus:ring-2 focus:ring-magenta focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#e9b949] border border-transparent rounded-full font-semibold text-xs text-[#3b2f26] uppercase tracking-widest hover:bg-[#efc45f] focus:bg-[#efc45f] active:bg-[#e0ad3a] focus:outline-none focus:ring-2 focus:ring-[#e9b949] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

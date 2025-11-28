<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-purple-700 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>

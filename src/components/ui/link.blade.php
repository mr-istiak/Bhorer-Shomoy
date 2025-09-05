@props(['href' => '#', 'varient' => 'primary', 'as' => 'a'])

<{{ $as }} {{ $as === 'a' ? 'href='.$href : '' }} {{ $attributes->merge(['class' => useClass([
    'hover:underline underline-4 underline-offset-4 transition-colors duration-300 hover:decoration-red-950 decoration-2 text-gray-900 hover:text-red-950' => $varient === 'link',
    'bg-gray-900 hover:bg-red-950 transition-all duration-300 px-4 py-1 rounded text-white font-semibold flex items-center justify-center text-center' => $varient === 'primary',
    'bg-gray-300 hover:bg-gray-50 border border-gray-300 hover:border-black transition-all duration-300 px-4 py-1 rounded text-black font-semibold flex items-center justify-center text-center' => $varient === 'secondery',
])]) }}>{{ $slot }}</{{ $as }}>

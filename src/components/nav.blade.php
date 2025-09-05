@props(['fixed' => true])

@use('App\Models\Category')

@php
$categories = Category::all(['bangla_name', 'slug', 'name']);
@endphp
<nav {{ $attributes->merge(['class' => useClass([ 
    'flex items-center justify-center' => true,  
    'py-2' => !$fixed,
    'group relative' => $fixed
])]) }}>
    @if ($fixed)
        <div class="w-6 h-6 absolute top-0 left-0 bg-transparent hidden group-focus-within:block"></div>
        <button class="text-gray-900 hover:text-red-950 active:text-red-950 w-6 h-6 cursor-pointer sm:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        </button>
    @endif
    <ul class="gap-4 flex-wrap justify-start {{ useClass([ 
        'sm:w-full lg:justify-between flex' => !$fixed,
        'absolute sm:static top-full w-36 sm:w-full left-0 bg-white shadow shadow-rose-100 sm:shadow-none z-40 sm:z-0 sm:mt-1 hidden sm:flex max-sm:group-focus-within:block flex-col sm:flex-row sm:justify-center' => $fixed 
    ]) }}"> 
        <li class="{{ useClass([ 'w-full sm:w-fit' => $fixed, 'w-fit' => !$fixed ]) }}"> 
            <x-ui.link href="{{ useUrl('latest') }}" title="Latest News" varient="link" class="{{ useClass(['px-4 py-2 inline-block w-full sm:p-0 sm:w-fit' => $fixed]) }}">সর্বশেষ</x-ui.link>
        </li>
        @foreach ($categories as $category) 
            <li class="{{ useClass([ 'w-full sm:w-fit' => $fixed, 'w-fit' => !$fixed ]) }}"> 
                <x-ui.link href="{{ useUrl($category->slug) }}" title="{{ $category->name }}" varient="link" class="{{ useClass(['px-4 py-2 inline-block w-full sm:p-0 sm:w-fit' => $fixed]) }}">{{ $category->bangla_name }}</x-ui.link>
            </li>
        @endforeach
    </ul>
</nav>
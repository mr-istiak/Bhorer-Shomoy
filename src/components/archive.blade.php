@props([ 'posts', 'name', 'banglaName', 'categories' => false, 'lazy' => 10 ])

<div class="flex md:flex-row flex-col w-full justify-between px-4 lg:px-0 gap-8">
    <section class="w-full md:w-[calc(100%-var(--container-xs))] xl:w-[calc(100%-var(--container-md))] bg-white shadow hover:shadow-md shadow-rose-100 transition-all duration-300 sm:px-12 sm:py-10 px-6 py-4 rounded-xl space-y-6"> 
        <h1 class="text-4xl font-semibold leading-snug text-slate-900 underline underline-offset-4" title="{{ $name }}">{{ $banglaName }}</h1>
        <div class="space-y-6">
            @if ($posts->count() === 0)
                <p class="text-2xl font-semibold text-red-900 text-center" title="No Post Found">কোন সংবাদ পৃষ্ঠাটিতে খুঁজে পাওয়া যায়নি</p>
            @else
                @php $post = $posts->shift(); @endphp
                <x-ui.hero :post="$post" :allow-categories="$categories"/>
                @foreach ($posts as $index => $post)
                    <x-post-overview :post="$post" :allow-categories="$categories" :lazy="$index >= $lazy"/>
                @endforeach
            @endif
        </div>
    </section>
    <x-layout.sidebar>
        {{ $slot }}    
    </x-layout.sidebar> 
</div>
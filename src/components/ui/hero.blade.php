@use("Carbon\Carbon")
@use("App\Models\Post")
@use("Illuminate\Support\Str")

@props(['post', 'allowCategories' => false, 'home' => false])
@php
    Carbon::setLocale('bn'); 
    $categories = $post->categories;
    $category = $post->categories->first();
    $slug = ($category ? $category->slug . '/' : '') . $post->slug;
@endphp

<div {{ $attributes }}>
    <div class="aspect-video overflow-hidden w-full bg-center bg-cover relative" style="background-image: url('{{ url($post->featuredImage?->path ?? '') }}')">
        <div class="w-full h-full bg-black/40 z-0 flex flex-col justify-end px-6 py-4 hover:bg-black/60 transition-all duration-300">
            <h2 class="text-lg sm:text-3xl md:text-xl font-semibold text-slate-50 hover:underline underline-offset-2 decoration-2 decoration-red-200 hover:text-red-200 transition-all duration-150 mb-1 {{ useClass([
                'lg:text-3xl' => !$home,
                'xl:text-3xl' => $home
            ]) }}">
                <x-ui.link href="{{ useUrl($slug) }}" varient="unstyled">
                    {{ $post->title }}
                </x-ui.link> 
            </h2> 
            @if($allowCategories)
                <ul class="gap-4 sm:flex flex-wrap text-slate-50 hidden">
                    @foreach ($categories as $index => $_category)
                        <li>
                            @if ($index == 0) 
                                <span>></span>
                            @endif
                            <x-ui.link href="{{ useUrl($_category->slug) }}" varient="unstyled" title="{{ $_category->name }}" class="hover:underline decoration-red-200 hover:text-red-200 decoration-1 transition-all duration-300">{{ $_category->bangla_name }}</x-ui.link>
                            @if ($index < count($categories)-1)
                                <span>,</span>
                            @endif
                        </li>    
                    @endforeach
                </ul> 
            @endif
            <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled" class="hidden mt-2 {{ useClass([
                'sm:inline-flex md:hidden lg:inline-flex' => !$home,
                '2xl:inline-flex' => $home
            ]) }}">
                <p class="text-sm text-slate-200 hover:text-white transition-colors duration-150">
                    {{ $post->meta_description ?? Str::limit(strip_tags( Post::find($post->id, ['content'])->content ), 200) }} 
                </p>
            </x-ui.link>
            <div class="w-full justify-between items-center mt-1 sm:m-0 xl:mt-4 {{ useClass([
                'sm:flex hidden md:hidden lg:flex' => !$home,
                'sm:flex hidden md:hidden 2xl:flex' => $home,
            ]) }}">
                <x-ui.link href="{{ useUrl($slug) }}" varient="link" class="underline text-rose-200 hover:text-white hover:decoration-white group link">
                    বিস্তারিত<span class="sr-only">about {{ $post->title }}</span><span aria-hidden="true" class="hidden group-[.link:hover]:inline-block">>></span>
                </x-ui.link>
                <p class="text-sm text-slate-100">
                    {{ enToBnNumber($post->published_at->diffForHumans()) }}
                </p>
            </div>
        </div>
    </div>
    <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled" class="flex mt-2 {{ useClass([
        'sm:hidden md:flex lg:hidden' => !$home,
        '2xl:hidden px-4' => $home
    ]) }}">
        <p class="text-sm text-slate-600 hover:text-slate-800 transition-colors duration-150">
            {{ $post->meta_description ?? Str::limit(strip_tags( Post::find($post->id, ['content'])->content ), 200) }} 
        </p>
    </x-ui.link>
    <div class="w-full justify-between items-center mt-1 sm:m-0 flex sm:hidden md:flex {{ useClass([
        'lg:hidden xl:mt-4' => !$home,
        '2xl:hidden 2xl:mt-4 px-4' => $home
    ]) }}">
        <x-ui.link href="{{ useUrl($slug) }}" varient="link" class="underline text-rose-800 group link">
            বিস্তারিত<span class="sr-only">about {{ $post->title }}</span><span aria-hidden="true" class="hidden group-[.link:hover]:inline-block">>></span>
        </x-ui.link>
        <p class="text-sm text-slate-500">
            {{ enToBnNumber($post->published_at->diffForHumans()) }}
        </p>
    </div>
</div>
@use("Carbon\Carbon")
@use("App\Models\Post")
@use("Illuminate\Support\Str")

@props(['post', 'design' => 'default', 'allowCategories' => false, 'lazy' => false, 'allowImage' => true, 'innerClass' => '', 'allowMeta' => true, 'allowExcerpt' => true])
@php
    Carbon::setLocale('bn'); 
    $categories = $post->categories;
    $category = $post->categories->first();
    $slug = ($category ? $category->slug . '/' : '') . $post->slug;
@endphp

@if(str_starts_with($design, 'shortRow'))
    <div {{ $attributes->merge(['class' => 'space-y-2 '.useClass([
        'bg-white rounded-xl overflow-hidden shadow hover:shadow-md shadow-rose-100 transition-all duration-300' => $design === 'shortRowCard',
    ]) ]) }}>
        <div class="flex flex-row w-full items-start justify-between {{ useClass([
            'gap-2' => $design !== 'shortRowCard',
            'gap-0.5' => $design === 'shortRowCard',
        ]) }}">
            <h2 class="w-full font-medium flex {{ useClass([
                'pl-4 text-base pt-2' => $design === 'shortRowCard',
                'text-lg sm:text-xl' => $design !== 'shortRowCard',
            ]) }}">
                <x-ui.link href="{{ useUrl($slug) }}" varient="link">
                    {{ $post->title }}
                </x-ui.link> 
            </h2> 
            @if($allowImage && isset($post->featuredImage) && $post->featuredImage?->path)
                <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled" class="flex w-full aspect-video object-center object-cover {{ useClass([
                    'max-w-32' => $design === 'shortRowCard',
                    'max-w-40' => $design !== 'shortRowCard',
                ]) }}">
                    <img src="{{ url($post->featuredImage?->path) }}" alt="{{ $post->featuredImage?->alt ?? $post->featuredImage?->title }}" title="{{ $post->featuredImage?->title }}" class="w-full hover:scale-98 aspect-video transition-all duration-300" {{ $lazy ? 'loading="lazy"' : '' }} />
                </x-ui.link> 
            @endif
        </div>
        <div class="{{ useClass([ 'px-4 pb-2' => $design === 'shortRowCard' ]) }}">
            @if($allowExcerpt)
                <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled">
                    <p class="text-sm text-slate-600 hover:text-slate-800 transition-colors duration-300 text-justify">
                        {{ $post->meta_description ?? Str::limit(strip_tags( Post::find($post->id, ['content'])->content ), 200) }} 
                    </p>
                </x-ui.link>
            @endif
            @if($allowMeta)
                <div class="flex w-full justify-between items-center mt-1">
                    <x-ui.link href="{{ useUrl($slug) }}" varient="link" class="underline text-rose-800 group link">
                        বিস্তারিত<span class="sr-only">about {{ $post->title }}</span><span aria-hidden="true" class="hidden group-[.link:hover]:inline-block">>></span>
                    </x-ui.link>
                    <p class="text-sm text-slate-500">
                        {{ enToBnNumber($post->published_at->diffForHumans()) }}
                    </p>
                </div>
            @endif
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => useClass([
        "flex flex-col-reverse" => true,
        "sm:flex-row gap-4" => $design !== 'column',
        "sm:gap-2" => $design === 'column',
    ])]) }}>
        <div class="w-full {{ $innerClass }} {{ useClass([
            'sm:w-[calc(100%-var(--container-2xs))] md:w-[calc(100%-12rem)] xl:w-[calc(100%-var(--container-2xs))]' => $design !== 'column',
        ]) }}">
            <h2 class=" font-medium {{ useClass([ 
                'mb-1' => $allowCategories && $design !== 'column', 
                'mb-4' => !$allowCategories && $design !== 'column',
                'text-2xl md:text-xl xl:text-2xl' => $design !== 'column',
                'text-lg leading-tight' => $design === 'column'
            ]) }}">
                <x-ui.link href="{{ useUrl($slug) }}" varient="link">
                    {{ $post->title }}
                </x-ui.link> 
            </h2> 
            @if($allowCategories)
                <ul class="gap-4 flex flex-wrap text-sm mb-3">
                    @foreach ($categories as $index => $_category)
                        <li>
                            @if ($index == 0) 
                                <span>></span>
                            @endif
                            <x-ui.link href="{{ useUrl($_category->slug) }}" varient="link" title="{{ $_category->name }}" class=" hover:decoration-1">{{ $_category->bangla_name }}</x-ui.link>
                            @if ($index < count($categories)-1)
                                <span>,</span>
                            @endif
                        </li>    
                    @endforeach
                </ul> 
            @endif
            @if($allowExcerpt)
                <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled" class="mt-2 flex">
                    <p class="text-sm text-slate-600 hover:text-slate-800 transition-colors duration-300 text-justify">
                        {{ $post->meta_description ?? Str::limit(strip_tags( Post::find($post->id, ['content'])->content ), 200) }} 
                    </p>
                </x-ui.link>
            @endif
            @if($allowMeta)
                <div class="flex w-full justify-between items-center mt-1">
                    <x-ui.link href="{{ useUrl($slug) }}" varient="link" class="underline text-rose-800 group link">
                        বিস্তারিত<span class="sr-only">about {{ $post->title }}</span><span aria-hidden="true" class="hidden group-[.link:hover]:inline-block">>></span>
                    </x-ui.link>
                    <p class="text-sm text-slate-500">
                        {{ enToBnNumber($post->published_at->diffForHumans()) }}
                    </p>
                </div>
            @endif
        </div>
        @if($allowImage && isset($post->featuredImage) && $post->featuredImage?->path)
            <x-ui.link href="{{ useUrl($slug) }}" varient="unsayled" class="w-full aspect-video {{ useClass([
                'sm:w-2xs md:w-48 xl:w-2xs' => $design !== 'column'
            ]) }}">
                <img src="{{ url($post->featuredImage?->path) }}" alt="{{ $post->featuredImage?->alt ?? $post->featuredImage?->title }}" title="{{ $post->featuredImage?->title }}" class="w-full hover:scale-98 aspect-video transition-all duration-300" {{ $lazy ? 'loading="lazy"' : '' }} />
            </x-ui.link> 
        @endif
    </div>
@endif





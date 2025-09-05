@use('App\Models\Post')
@use('App\Models\Position')

@php
    $positions = Position::with('post')->whereNotNull('post_id')
        ->where('name', 'not like', 'right-%')->get();
    $mainPosts = Post::public()->with(['featuredImage', 'categories'])
        ->orderBy('published_at', 'desc')
        ->limit(16 - $positions->count())->get();
        
    $middlePositions = $positions->filter(fn($p) => str_starts_with($p->name, 'featured')
            || str_starts_with($p->name, 'main-')
            || str_starts_with($p->name, 'secondary-'))->keyBy('name'); // so we can access by name quickly

    $qurriedmiddlePosts = $mainPosts->slice(0, 9 - $middlePositions->count())->values();

    $middlePosts = collect(range(0, 8))->map(function ($i) use ($middlePositions, &$qurriedmiddlePosts) {
        // decide the expected slot name
        if ($i <= 1) $slot = 'featured' . ($i === 0 ? '' : '-2');
        elseif ($i <= 5) $slot = 'main-' . ($i - 1);
        else $slot = 'secondary-' . ($i - 5);
        // use positioned post if available, otherwise fallback
        return $middlePositions->has($slot) ? $middlePositions[$slot]->post : $qurriedmiddlePosts->shift();
    });

    $leftPositions = $positions->filter(fn($p) => str_starts_with($p->name, 'left-'))->keyBy('name');
    $leftPosts = collect([]);
    if($mainPosts->count() > (9 - $middlePositions->count())) {
        $quirriedleftPosts = $mainPosts->slice(9 - $middlePositions->count(), 7-$leftPositions->count())->values();
        $leftPosts = collect(range(0, 6))->map(function ($i) use ($leftPositions, &$quirriedleftPosts) {
            $slot = 'left-' . ($i + 1);
            // use positioned post if available, otherwise fallback
            return $leftPositions->has($slot) ? $leftPositions[$slot]->post : $quirriedleftPosts->shift();
        });
    } else $leftPosts = $leftPositions->map(fn($p) => $p->post);

@endphp 
<x-layout.app class="px-4 lg:px-0 gap-12 items-center">
    <div class="w-4/5 h-32 flex bg-black text-white text-center"> 
        Ad
    </div>
    <section class="w-full h-full flex flex-col md:flex-row gap-4">
        <div class="w-full h-full flex flex-col-reverse xl:flex-row gap-4">
            <div class="w-full xl:max-w-3xs flex flex-col gap-2">
                @foreach ($leftPosts as $index => $post)
                    @if($index < 4)
                        @if($index == 0)
                            <div class="w-full xl:flex flex-col flex sm:grid grid-cols-2 gap-2">
                        @endif    
                            <x-post-overview :post="$post" design="shortRowCard" :allow-image="$index < 2" :allow-meta="$index < 2" />
                        @if($index == 3)
                            </div>
                        @endif
                    @else
                        @if($index == 4)
                            <div class="bg-white rounded-xl overflow-hidden shadow hover:shadow-md shadow-rose-100 transition-all duration-300 py-1 divide-y divide-red-950">
                        @endif
                                @php
                                    $category = $post->categories->first();
                                    $slug = ($category ? $category->slug . '/' : '') . $post->slug;
                                @endphp
                                <h2 class="w-full font-medium px-4 flex text-base py-1">
                                    <x-ui.link href="{{ useUrl($slug) }}" varient="link">
                                        {{ $post->title }}
                                    </x-ui.link> 
                                </h2> 
                        @if($index == $leftPosts->count() - 1)
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
            <div class="bg-white rounded-xl shadow hover:shadow-md shadow-rose-100 transition-all duration-300 w-full flex flex-col overflow-hidden pb-2 justify-between">  
                <div class="w-full flex sm:gap-2 gap-4 items-start justify-between sm:flex-row flex-col pb-2">
                    @if($middlePosts->count() > 0)
                        @php $post = $middlePosts->shift(); @endphp
                        <x-ui.hero :post="$post" :allow-categories="true" class="flex flex-col" :home="true" />
                    @endif
                    @if($middlePosts->count() > 0)
                        @php $post = $middlePosts->shift(); @endphp
                        <x-post-overview :post="$post" design="column" class="w-full xl:max-w-48" inner-class="pr-4 pl-4 sm:pl-0 mt-4 sm:mt-0" />
                    @endif
                </div>
                <div class="sm:grid grid-cols-2 gap-4 w-full py-4 md:hidden">
                    <div class="w-full aspect-[4/3] flex bg-black text-white text-center">
                        Ad
                    </div>
                    <div class="hidden w-full aspect-[4/3] sm:flex bg-black text-white text-center">
                        Ad
                    </div>
                </div>
                @if($middlePosts->count() > 0)
                    <div class="grid sm:grid-cols-2 grid-cols-1 w-full border-t border-red-950 pt-2 2xl:border-0">
                        @php
                            $frontshortRowPosts = $middlePosts->slice(0, 4)->values();
                            if($middlePosts->count() > 4) $middlePosts = $middlePosts->slice(4)->values();
                            else $middlePosts = [];
                        @endphp
                        @foreach ($frontshortRowPosts as $key => $post)
                            <x-post-overview :post="$post" design="shortRow" class="w-full pb-2 {{ useClass([
                                'border-t border-red-950 pt-2' => ($key == 2) || ($key == 3),
                                'pl-3 pr-4' => ($key == 1) || ($key == 3),
                                'pr-3 pl-4' => ($key == 0) || ($key == 2),
                                'border-t border-red-950 pt-2 sm:border-0 sm:pt-0' => $key == 1
                            ]) }}" :allow-categories="true" :allow-image="($key == 0) || ($key == 1)" />
                        @endforeach 
                    </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-3 w-full items-start sm:px-4 sm:gap-4">
                    @foreach ($middlePosts as $post)
                        <x-post-overview :post="$post" design="column" :allow-excerpt="false" class="border-t px-4 sm:px-0 border-red-950 py-2 sm:border-0" />
                    @endforeach
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow hover:shadow-md shadow-rose-100 transition-all duration-300 md:max-w-3xs lg:max-w-xs w-full flex relative flex-col overflow-hidden xl:justify-between">
            <div class="w-full aspect-[4/3] md:flex hidden bg-black text-white text-center">
                Ad
            </div>
            <x-ui.sidebarPosts />
            <div class="w-full aspect-[4/3] md:flex hidden bg-black text-white text-center">
                Ad
            </div>
        </div>
    </section>
    <div class="w-9/10 h-64 flex bg-black text-white text-center"> 
        Ad
    </div>
    <section class="w-full space-y-6">
        <h3 class="text-2xl font-semibold underline underline-offset-4  hover:decoration-red-950 hover:text-red-950 transition-all duration-300 cursor-pointer">সর্বশেষ খবর</h3>
        <div class="w-full grid md:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-6">
            @foreach ($mainPosts as $index => $post)
                @if($index < 4)
                    <x-post-overview :post="$post" design="column" class="bg-white rounded-xl overflow-hidden shadow hover:shadow-md shadow-rose-100 transition-all duration-300 justify-end pb-4" innerClass="px-4 mt-4 sm:mt-0" />
                @endif
            @endforeach
        </div>
    </section>
    <section v-scope="{ 
        alreadyHandled: false,
        el: null,
        firstInteractionEvents: [ 'click','mousedown','mouseup','mousemove','keydown','keyup','scroll','touchstart', 'touchmove', 'wheel', 'pointerdown' ],
        async handleFirstInteraction() {
            if (this.alreadyHandled) return;
            this.alreadyHandled = true;
            const { newContent } = await this.loadUrl('{{ useUrl('categories', true) }}', '#app');
            this.el.innerHTML = newContent.innerHTML;
        },
        init(el) {
            this.el = el;
            this.firstInteractionEvents.forEach(type => {
                window.addEventListener(type, this.handleFirstInteraction, { once: true, capture: true });
            });
        } 
    }" @@vue:mounted="init($el)" class="w-full flex flex-col gap-12"></section>
</x-layout.app>
@use('App\Models\Post')
@use('App\Models\Position')

@php
    $positions = Position::with('post')->whereNotNull('post_id')->where('name', 'like', 'right-%')->get()->keyBy('name');
    $qurriedposts = collect([]);
    if ($positions->count() < 3 ) {
        $qurriedposts = Post::public()
            ->with(['featuredImage', 'categories'])
            ->whereHas('categories', fn($q) => $q->where('slug', 'opinion'))
            ->orderBy('published_at', 'desc')
            ->limit(3-$positions->count())
            ->get(); 
    }
    $total = $positions->count() + $qurriedposts->count();
    $posts = [];

    for ($i = 0; $i < $total; $i++) {
        $slot = 'right-' . ($i + 1);
    
        if ($positions->has($slot)) {
            $posts[] = $positions[$slot]->post;
        } else {
            $posts[] = $qurriedposts->shift();
        }
    }
@endphp
<div class="divide-y divide-red-950">
    @foreach ($posts as $key => $post)
        <x-post-overview :post="$post" design="shortRow" class="w-full py-2 px-4" :allow-excerpt="($key == 0)" :allow-meta="($key == 0)"  /> 
    @endforeach
</div>
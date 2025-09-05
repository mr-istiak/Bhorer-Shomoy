@use('App\Models\Category')
@use('App\Enums\PostStatus')

@php
    $categories = Category::with([
        'posts' => fn($query) => $query->with(['featuredImage', 'categories'])->where('status', PostStatus::PUBLISHED->value)->limit(5)->orderBy('published_at', 'desc')
    ])->limit(5)->get();
@endphp

<x-layout.app title="Categories" class="gap-16">
    @php
        $firstCategory = $categories->shift();
    @endphp
    <x-archive :posts="$firstCategory->posts" :name="$firstCategory->name" :bangla-name="$firstCategory->bangla_name"/>
    @foreach ($categories as $category)
        @php
            if($category->posts->count() === 0) continue;
        @endphp
        <x-archive :posts="$category->posts" :name="$category->name" :bangla-name="$category->bangla_name" :lazy="0">
            <div class="w-full aspect-[4/3] flex bg-black text-white text-center">
                Ad
            </div>
        </x-archive>
    @endforeach
</x-layout.app>
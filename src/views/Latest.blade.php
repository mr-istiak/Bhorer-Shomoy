@use('App\Models\Post')

@php
$posts = Post::public()->with(['featuredImage', 'categories'])->orderBy('published_at', 'desc')->get();
@endphp

<x-layout.app title="Latest News">
    <x-archive :posts="$posts" name="Latest News" banglaName="সর্বশেষ" :categories="true"  /> 
</x-layout.app>
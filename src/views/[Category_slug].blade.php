@use('App\Enums\PostStatus')

@props([ 'model' ])
@php
    $posts = $model->posts()->where('status', PostStatus::PUBLISHED->value)->with(['featuredImage', 'categories'])->orderBy('published_at', 'desc')->get();
@endphp 
<x-layout.app title="{{ $model->name }}">
    <x-archive :posts="$posts" :name="$model->name" :banglaName="$model->bangla_name" />
</x-layout.app>
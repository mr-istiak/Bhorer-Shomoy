@props([ 'model' ])
@php
    $categories = $model->categories->all();   
    $featuredImage = $model->featuredImage;
@endphp
<x-layout.app class="" title="{{ $model->title }}">
    <div class="flex md:flex-row flex-col w-full justify-between px-4 lg:px-0 gap-8">
        <section class="w-full md:w-[calc(100%-var(--container-xs))] xl:w-[calc(100%-var(--container-md))] bg-white shadow hover:shadow-md shadow-rose-100 transition-all duration-300 sm:px-12 sm:py-10 px-6 py-4 rounded-xl space-y-6"> 
            {{-- Category --}} 
            <ul class="gap-4 flex flex-wrap text-lg">
                @foreach ($categories as $index => $category)
                    <li> 
                        <x-ui.link href="{{ useUrl($category->slug) }}" varient="link" class="underline" title="{{ $category->name }}">{{ $category->bangla_name }}</x-ui.link>
                        @if ($index < count($categories)-1)
                            <span>,</span>
                        @endif
                    </li>    
                @endforeach
            </ul> 
            <h1 class="sm:text-3xl lg:text-4xl text-2xl font-semibold leading-snug text-slate-900">{{ $model->title }}</h1>
            <div class="w-full flex justify-between items-end mb-2">
                <ul class="text-sm text-slate-900">
                    <li>প্রকাশ: {{ formatBnTime($model->published_at) }}</li>
                    @if($model->updated_at != $model->published_at) 
                        <li>আপডেট: {{ formatBnTime($model->updated_at) }}</li>
                    @endif 
                </ul>
                <x-ui.share :title="$model->title" :url="useUrl($model->slug, true)" />
            </div>
            <div class="flex flex-col gap-6 items-center justify-between w-full border-t pt-4 border-red-950">
                @if($featuredImage)
                    <img src="{{ url($featuredImage->path) }}" alt="{{ ($model->featuredImage->alt ?? $model->featuredImage->title) ?? $model->title }}" class="w-full h-full aspect-auto max-w-full object-cover"> 
                @endif
                <div class="w-full prose prose-p:m-0 max-w-max text-justify"> 
                    {!! $model->content !!}
                </div>
            </div>
        </section>
        <x-layout.sidebar /> 
    </div>
</x-layout.app>
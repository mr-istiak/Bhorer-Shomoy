<section class="md:w-xs xl:w-md w-full bg-white shadow hover:shadow-md shadow-rose-100 transition-all duration-300 rounded-xl overflow-hidden space-y-4">
    @if ($slot->isEmpty())
        <div class="w-full aspect-[4/3] flex bg-black text-white text-center">
            Ad
        </div>
        <x-ui.sidebarPosts />
    @else
        {{ $slot }}
    @endif
</section>
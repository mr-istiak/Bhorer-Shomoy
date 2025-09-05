@props([ 'title' => 'Check this out!', 'url' => useUrl('', true) ])
@php
    $url = urlencode($url); // current page url
    $text = urlencode($title);
@endphp

<div class="flex space-x-2">
    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank" 
       class="text-blue-600 hover:text-blue-800">
        <x-icons.facebook />
    </a>
  
    <!-- Twitter (X) -->
    <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $text }}" target="_blank" 
       class="text-slate-800 hover:text-black">
        <x-icons.X />
    </a>

    <!-- WhatsApp -->
    <a href="https://wa.me/?text={{ $text }}%20{{ $url }}" target="_blank" 
       class="text-green-600 hover:text-green-800">
        <x-icons.whatsapp />
    </a>
</div>

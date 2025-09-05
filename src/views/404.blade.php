<x-layout.app title="404 Page Not Found">
    <div class="flex md:flex-row flex-col w-full justify-between px-4 lg:px-0 gap-8">
        <section class="w-full md:w-[calc(100%-var(--container-xs))] xl:w-[calc(100%-var(--container-md))] bg-white shadow hover:shadow-md shadow-rose-100 transition-all duration-300 sm:p-12 p-6 rounded-xl flex flex-col items-center"> 
            <h1 class="text-9xl font-semibold text-red-700" title="404">৪০৪</h1>
            <p class="text-2xl font-semibold text-slate-900" title="Page Not Found">পৃষ্ঠাটি খুঁজে পাওয়া যায়নি</p>
            <x-ui.link href="/" varient="primary" class="px-6 py-4 text-xl mt-6">হোমে ফিরে যান</x-ui.link>
        </section>
        <x-layout.sidebar /> 
    </div>
</x-layout.app>
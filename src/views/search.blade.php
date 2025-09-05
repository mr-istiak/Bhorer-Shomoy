<x-layout.app title="Search">
    <div v-scope="{
        search: new URLSearchParams(window.location.search).get('search'),
        posts: [],
        async mounted() {
            try {
                const res = await fetch(`{{ route('api.search') }}?search=${this.search}`, {
                    method: 'GET', 
                    headers: {
                        'Accept': 'application/json', 
                        'Authorization': '{{ apiAuthToken() }}'
                    }
                });
                const data = await res.json();
                this.posts = data.posts
                console.log(data);
            } catch (error) {
                console.log(error);
            }
        }
    }" @vue:mounted="mounted" class="flex md:flex-row flex-col w-full justify-between px-4 lg:px-0 gap-8">
        <section class="w-full md:w-[calc(100%-var(--container-xs))] xl:w-[calc(100%-var(--container-md))] bg-white shadow hover:shadow-md shadow-rose-100 transition-all duration-300 sm:px-12 sm:py-10 px-6 py-4 rounded-xl space-y-6"> 
            <h1 class="text-4xl font-semibold leading-snug text-slate-900 underline underline-offset-4" :title="search" v-effect="$el.innerText = search"></h1>
            <div>
                <p v-if="posts.length === 0" class="text-2xl font-semibold text-red-900 text-center" title="No Post Found">কোন সংবাদ পৃষ্ঠাটিতে খুঁজে পাওয়া যায়নি</p>
                <div v-else class="space-y-6">
                    {{-- <x-post-overview :post="$post" :allow-categories="$categories" :lazy="$index >= $lazy"/> --}}
                    <div v-for="(post, index) in posts" class="flex flex-col-reverse sm:flex-row gap-4">
                        <div class="w-full sm:w-[calc(100%-var(--container-2xs))] md:w-[calc(100%-12rem)] xl:w-[calc(100%-var(--container-2xs))]">
                            <h2 class="font-medium mb-1 text-2xl md:text-xl xl:text-2xl">
                                <x-ui.link ::href="`{{ useUrl('${post.slug}') }}`" varient="link" v-text="post.title" /> 
                            </h2> 
                            <ul class="gap-4 flex flex-wrap text-sm mb-3">
                                <li v-for="(category, key) in post.categories">
                                    <span v-if="key == 0">></span>
                                    <x-ui.link ::href="`{{ useUrl('${post.slug}') }}`" varient="link" ::title="category.name" class="hover:decoration-1" v-text="category.bangla_name" />
                                    <span v-if="key < (post.categories.length - 1)">,</span>
                                </li>    
                            </ul>
                            <x-ui.link ::href="`{{ useUrl('${post.slug}') }}`" varient="unsayled" class="mt-2 flex">
                                <p class="text-sm text-slate-600 hover:text-slate-800 transition-colors duration-300 text-justify" v-text="post.meta_description"></p>
                            </x-ui.link>
                            <div class="flex w-full justify-between items-center mt-1">
                                <x-ui.link ::href="`{{ useUrl('${post.slug}') }}`" varient="link" class="underline text-rose-800 group link">
                                    বিস্তারিত<span class="sr-only">about @{{ post.title }}</span><span aria-hidden="true" class="hidden group-[.link:hover]:inline-block">>></span>
                                </x-ui.link>
                                <p class="text-sm text-slate-500" v-text="enToBnNumber(post.published_at ?? '')"></p>
                            </div>
                        </div>
                        <x-ui.link v-if="post.featured_image && post.featured_image?.path" ::href="`{{ useUrl('${post.slug}') }}`" varient="unsayled" class="w-full aspect-video sm:w-2xs md:w-48 xl:w-2xs">
                            <img :src="`{{ url('${post.featured_image?.path}') }}`" :alt="post.featured_image?.alt ?? post.featured_image?.title" :title="post.featured_image?.title" class="w-full hover:scale-98 aspect-video transition-all duration-300"/>
                        </x-ui.link> 
                    </div>
                </div>
            </div>
        </section>
        <x-layout.sidebar />
    </div>
</x-layout.app>
<x-layout.app title="ইপেপার">
    <div v-scope="{
        next: null,
        previous: null,
        content: null,
        epaperId: new URLSearchParams(window.location.search).get('epaper'),
        type: new URLSearchParams(window.location.search).get('type') || 'text',
        epageNumber: new URLSearchParams(window.location.search).get('page_number'),
        async mounted() {
            try {
                const articleId = new URLSearchParams(window.location.search).get('id');
                const res = await fetch(`{{ url('api/epaper/article') }}/${articleId}?type=${this.type}`, {
                    method: 'GET', 
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': '{{ apiAuthToken() }}'
                    }
                });
                const data = await res.json();
                this.content = data.article.content;
                this.next = data.next;
                this.previous = data.previous;
            } catch (error) {
                console.log(error);
            }
        },
        viewTypedLink(type) {
            const url = new URL(window.location.href);
            if(url.searchParams.has('type')) url.searchParams.delete('type');
            url.searchParams.append('type', type);
            return url.href;
        }
    }" @vue:mounted="mounted" class="w-full px-4 sm:p-0 space-y-4">
        <div class="bg-white sm:px-12 sm:py-10 px-6 py-4 rounded-xl shadow hover:shadow-md shadow-rose-100 transition-all duration-300 w-full flex gap-4">
            <x-ui.link varient="secondery" class="gap-1" ::href="`{{ useUrl('epaper?id=${epaperId}${epageNumber ? (`&page_number=${epageNumber}`): ``}') }}`">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                <div class="pt-1">Go back to page</div>
            </x-ui.link>
            <x-ui.link varient="secondery" class="gap-1" ::href="viewTypedLink('text')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <div class="pt-1">Text View</div>
            </x-ui.link>
            <x-ui.link varient="secondery" class="gap-1" ::href="viewTypedLink('image')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
                <div class="pt-1">Image View</div>
            </x-ui.link>
            <x-ui.link v-if="previous" varient="secondery" class="gap-1" v-effect="$el.href = `{{ useUrl('epaper/article?epaper=${epaperId}&id=${previous}') }}`">
                <div class="pt-1">Previous Article</div>
            </x-ui.link>
            <x-ui.link v-if="next" varient="secondery" class="gap-1" v-effect="$el.href = `{{ useUrl('epaper/article?epaper=${epaperId}&id=${next}') }}`">
                <div class="pt-1">Next Article</div>
            </x-ui.link>
        </div>
        <div class="bg-white sm:px-12 sm:py-10 px-6 py-4 rounded-xl shadow hover:shadow-md shadow-rose-100 transition-all duration-300 w-full">
            <div v-effect="$el.innerHTML = content" class="prose max-w-full text-justify"></div>
        </div>
    </div>
</x-layout.app>
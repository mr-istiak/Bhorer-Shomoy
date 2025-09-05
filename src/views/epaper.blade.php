<x-layout.app title="ইপেপার" class="items-center justify-center">
    <div v-scope="{
        slider: null,
        dpi: null,
        currentIndex: 0,
        totalPages: 0,
        pages: [],
        pageImages: {},
        epaper: {
            title: '',
            'id': '',
            created_at: '',
        },
        async mounted() {
            try {
                const res = await fetch('{{ route('api.epaper.index') }}', {
                    method: 'GET', 
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': '{{ apiAuthToken() }}'
                    }
                });
                const data = await res.json();
                this.pages = data.epaper.pages;
                this.dpi = data.dpi
                this.epaper.id = data.epaper.id;
                this.epaper.title = data.epaper.title;
                this.epaper.created_at = data.epaper.created_at;
                this.totalPages = this.pages.length || 0;
            } catch (error) {
                console.log(error);
            }
        },
        get pageWidth() {
            return this.slider?.clientWidth || 0;
        },
        next() {
            if (this.currentIndex < this.totalPages - 1) {
                this.currentIndex++;
                this.slider.scrollTo({ left: this.currentIndex * this.pageWidth, behavior: 'smooth' });
                const url = new URL(window.location.href);
                if(url.searchParams.has('page_number')) {
                    url.searchParams.delete('page_number')
                    history.pushState(null, '', url);
                }
            }
        },
        prev() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.slider.scrollTo({ left: this.currentIndex * this.pageWidth, behavior: 'smooth' });
                const url = new URL(window.location.href);
                if(url.searchParams.has('page_number')) {
                    url.searchParams.delete('page_number')
                    history.pushState(null, '', url);
                }
            }
        },
        updateIndex() {
            if (this.slider) this.currentIndex = Math.round(this.slider.scrollLeft / this.pageWidth);
        },
        position(rectPDF, el, page) {
            const img = this.pageImages[page.page_number]
            if(!img) return;
            const scale = Math.min(img.width / img.naturalWidth, img.height / img.naturalHeight);
            rectScreen = this.pdfToImgRect(rectPDF, this.dpi, img.height, scale);
            el.style.width = `${ rectScreen.width }px`;
            el.style.height = `${ rectScreen.height }px`;
            el.style.top = `${ rectScreen.y }px`;
            el.style.left = `${ rectScreen.x }px`;
        },
        startSlider() {
            this.currentIndex = (new URLSearchParams(window.location.search)).has('page_number') ? Number(new URLSearchParams(window.location.search).get('page_number')) - 1 : 0
            setTimeout(() => {
                this.slider.scrollTo({ left: this.currentIndex * this.pageWidth, behavior: 'smooth' })
            }, 100) 
        }
      }" class="relative w-full overflow-hidden flex flex-col" @vue:mounted="mounted">
    <!-- Slider container -->
        <div @vue:mounted="slider = $el" 
            @scroll="updateIndex"
            class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth no-scrollbar">
        <!-- Page 1 -->
            <div v-for="(page, index) in pages" class="min-w-full snap-start shadow-xl rounded-xl relative" @vue:mounted="((pages.length - 1) == index) ? startSlider() : null">
                <img :src="`{{ url('/') }}/${page.image_path}`" :alt="`Page ${ page.page_number }`" @load="pageImages[page.page_number] = $event.target" class="w-full">
                <x-ui.link v-for="articleBox in page.article_boxes" ::href="`{{ useUrl('epaper/article?epaper=${epaper.id}&page_number=${page.page_number}&id=${articleBox.article_id}') }}`" varient="unstyled" class="p-4 bg-transparent hover:bg-red-500/10 absolute z-20 cursor-pointer transition-colors" v-effect="position(articleBox.bounding_box, $el, page)"></x-ui.link>
            </div>
        </div>
        <!-- Navigation arrows -->
        <button v-if="currentIndex > 0" @click="prev" 
                class="fixed top-1/2 -translate-y-1/2 bg-black/60 text-white px-4 py-2 rounded-full hover:bg-black transition cursor-pointer">
        ←
        </button>
        <button v-if="currentIndex < totalPages - 1" @click="next" 
                class="fixed top-1/2 -translate-y-1/2 self-end bg-black/60 text-white px-4 py-2 rounded-full hover:bg-black transition cursor-pointer">
        →
        </button>
        <!-- Optional page indicators -->
        <div class="absolute bottom-3 w-full flex justify-center space-x-2">
            <span v-for="n in totalPages" :class="['w-3 h-3 rounded-full', n-1 === currentIndex ? 'bg-black' : 'bg-gray-400']"></span>
        </div>
    </div>
</x-layout.app>
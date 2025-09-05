<div v-scope="{
    searchText: '',
    container: null,
    hide() {
        setTimeout(() => {
            this.searchText = ''
            if(!this.container) return;
            this.container.querySelector(':focus')?.blur();
        }, 100)
    }
}" class="w-fit flex flex-col relative items-center justify-center group" @vue:mounted="container = $el">
    <button class="cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
    </button>
    <div class="absolute top-full -left-[20dvw] sm:left-0 w-72 bg-white shadow-sm shadow-rose-100 overflow-hidden z-50 gap-2 pr-4 items-center justify-between mt-3 group-focus-within:flex hidden focus-within:ring focus-within:ring-rose-200">
        <input type="text" v-model="searchText" name="search" id="search" class="bg-transparent pl-4 py-2 w-full !outline-0" placeholder="Search">
        <x-ui.link v-effect="$el.href = `{{ useUrl('search?search=${searchText}') }}`" @click="hide" varient="unstyled">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </x-ui.link>
    </div>
</div>
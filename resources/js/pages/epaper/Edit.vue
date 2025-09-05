<script lang="ts">
export default {
    breadcrumbs: [
        { title: 'Epaper', href: route('epapers.index') },
        { title: 'Configure Epaper', href: document.location.href },
    ]
};
</script>

<script setup lang="ts">
import { Epaper } from '@/types';
import { computed, ref } from 'vue';
import { Settings } from 'lucide-vue-next';
import { Head, Link, router } from '@inertiajs/vue3';
import { makeUrl } from '@/composables/useUrl';
import { Button } from '@/components/ui/button';
import InlineHeader from '@/components/InlineHeader.vue';

defineOptions({ inheritAttrs: false })

const props = defineProps<{
    epaper: Epaper
}>()

const generating = ref(false);

const articleId = computed(() => (new URLSearchParams(window.location.search)).get('article'))

const generate = () => {
    if(generating.value) return;
    generating.value = true
    router.post(route('epaper.generate', props.epaper.id), {} , {
        onFinish: () => {
            generating.value = false
        }
    });
}
</script>

<template>
    <Head title="Configure Epaper" />
    <div class="p-6 gap-4 flex flex-col">
        <InlineHeader title="Configure Epaper" :link="{ href: route('epapers.index'), text: 'Back to Epapers' }" :description="'Epaper title: '+ epaper.title" />
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8">
            <template v-for="page in props.epaper.pages" :key="page.id">
                <div class="border rounded-lg overflow-hidden relative">
                    <Link :href="route('epages.show', { 'epage' : page.id, 'extending_article': articleId })">
                        <img :src="makeUrl(page.image_path)" :alt="`page_${page.page_number}`" class="w-fit h-fit object-contain cursor-pointer hover:opacity-60 transition-opacity duration-300 bg-primary" />
                    </Link>
                    <div class="flex py-2 px-2 items-center justify-between">
                        <h2 class="text-sm font-medium text-primary truncate">{{ `Page ${page.page_number}` }}</h2>
                        <Button :as="Link" :href="route('epages.show', { 'epage' : page.id, 'extending_article': articleId })" variant="ghost" size="sm">
                            <Settings class="h-4 w-4 text-primary" />
                        </Button>
                    </div>
                </div>
            </template>
        </div>
        <Button v-if="!articleId" @click="generate" class="mt-4 self-end" :disabled="generating">{{ generating ? 'Generating...' : 'Generate'}}</Button>
    </div>
</template>
<script lang="ts">
export default {
    breadcrumbs: [
        {
            title: 'Media',
            href: route('media.index'),
        }
    ]
};
</script>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import FileUploader from '@/components/FileUploader.vue';
import { Paginated, Media } from '@/types';
import { defineAsyncComponent, ref } from 'vue';
import ImageOverview from '@/components/ImageOverview.vue';

const Paginator = defineAsyncComponent(() => import("@/components/ui/pagination/Paginator.vue"));
defineOptions({ inheritAttrs: false })

const props = defineProps({
    media: {
        required: true,
        type: Object as () => Paginated<Media>
    },
    select: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['newUpload', 'fileRemoved']);

const model = defineModel<Media | null>({ required: false });

const editingFile = ref<Media | null>(null);
const open = ref(false);

const edit = (media: Media) => {
    editingFile.value = media;
    open.value = true;
}

const selectFile = (media: Media) => {
    if(!props.select) return;
    model.value = media
}

</script>

<template>
    <Head title="Media" />
    <div class="p-4">
        <!-- Page Header with Title and Upload Button -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Media</h1>
            <FileUploader v-model:defaultMedia="editingFile" v-model:open="open" @uploaded="emit('newUpload')"/>
        </div>
        <!-- Show The Images, it will be in square areas within columns and rows accroding the screen size -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            <template v-for="item in props.media.data" :key="item.id">
                <ImageOverview :item="item" :select="select" :selection="model" @click="select ? selectFile(item) : edit(item)" @removed="emit('fileRemoved', item)"/>
            </template>
        </div>
        <Paginator v-if="media.total > media.per_page" :pagiantion-data="media" base-route-name="media.index" class="mt-4"/>
    </div>
</template>

<script setup lang="ts">
import Media from '@/pages/media/Index.vue'
import { Media as MediaType, Paginated } from '@/types';
import { ref, watch } from 'vue';
import Button from './ui/button/Button.vue';
import { UploadCloud } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger, DialogClose } from '@/components/ui/dialog';
import axios from 'axios';

const model = defineModel<MediaType | null>({ required: true })
const open = defineModel<boolean>('open', { required: false });

const selection = ref<MediaType | null>(null);
/* Fetch All Files */
const media = ref<Paginated<MediaType> | null>(null);
const getMedia = async () => {
    const response = await axios.get(`${route('media.index')}?jsonResponse=true`);
    media.value = response.data.media;
}

watch(() => open.value, value => {
    if(!value) {
        selection.value = null;
        return;
    }
    getMedia();
    selection.value = model.value;
})
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot name="trigger">
                <Button as="span" variant="link" class="inline-flex items-center">
                    <UploadCloud class="h-4 w-4" />
                    Select Image
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent as="div" class="!max-w-[90dvw] max-h-[80dvh] h-full overflow-y-auto">
            <DialogHeader>
                <DialogTitle class="hidden">Select Image</DialogTitle>
                <DialogDescription class="hidden">
                    Select Your Image.
                </DialogDescription>
            </DialogHeader>
            <Media v-if="media" :media="media" :select="true"  v-model="selection" @new-upload="getMedia()" @file-removed="getMedia()"/>
            <DialogFooter>
                <DialogClose>
                    <Button :disabled="!selection" @click="model = selection">Select</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { UploadCloud } from 'lucide-vue-next';
import { Progress } from '@/components/ui/progress';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger, DialogClose } from '@/components/ui/dialog';
import type { Media } from '@/types';
import { makeUrl } from '@/composables/useUrl';
import { defineAsyncComponent } from 'vue';

const UserInfo = defineAsyncComponent(() => import('@/components/UserInfo.vue'));

const defaultMedia = defineModel('defaultMedia', {
    type: Object as () => Media | null,
    default: null,
});

const emit = defineEmits(['uploaded']);

const form = useForm({
    file: null as File | null,
    title: '',
    alt: '',
});

const preview = () => {
    if(defaultMedia.value) return makeUrl(defaultMedia.value.path);
    return form.file ? URL.createObjectURL(form.file) : '';
}

const action = computed(() => defaultMedia.value ? 'edit' : 'upload');

const setImage = (file: File) => {
    form.file = file;
    form.title = file.name.replace(/\.[^/.]+$/, ''); // Remove file extension
    form.alt = file.name.replace(/\.[^/.]+$/, '');
}

watch(() => defaultMedia.value, (newVal) => {
    if(newVal) {
        // Do NOT create a synthetic File from URL. Only set the editable fields.
        form.file = null;
        form.title = newVal.title || '';
        form.alt = newVal.alt || '';
    }
});

const reset = () => {
    defaultMedia.value = null;
    form.reset();
}

const submit = () => {
    if(defaultMedia.value) {
        form.put(route('media.update', defaultMedia.value.id), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                reset();
            },
        });
        return;
    }
    form.post(route('media.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            reset();
            emit('uploaded');
        },
    });
}
</script>

<template>
    <Dialog v-bind="$attrs">
        <DialogTrigger as-child @click="reset()">
            <slot name="trigger">
                <Button as="span" variant="link" class="inline-flex items-center">
                    <UploadCloud class="h-4 w-4" />
                    Upload File
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent>
            <DialogHeader>
                <DialogTitle class="capitalize">{{ action }} Image</DialogTitle>
                <DialogDescription>
                    {{ action.charAt(0).toUpperCase() + action.slice(1, action.length) }} your image here. Click {{ action }} when you're done.
                    <Progress v-if="form.progress" v-model="form.progress.percentage" />
                    <div v-if="defaultMedia && defaultMedia.author" class="flex items-center p-2 pt-0 text-xs text-muted-foreground space-x-2 mt-2">
                        <span>Uploaded by</span>
                        <UserInfo :user="defaultMedia.author" :show-email="true" />
                    </div>
                </DialogDescription>
            </DialogHeader>
            <form @submit.prevent="submit()" class="space-y-4">
                <section class="overflow-y-auto max-h-[60vh] space-y-4">
                    <!-- Preview In Browser-->
                    <div v-if="form.file || defaultMedia" class="mt-4">
                        <Label>Preview:</Label>
                        <img :src="preview()" alt="Image Preview" class="max-w-full h-auto mt-2 rounded" />
                    </div>
                    <template v-if="!defaultMedia">
                        <Label for="file">Select Image</Label>
                        <!-- name changed to 'file' to match useForm key -->
                        <Input type="file" id="file" name="file" accept="image/*" @input="setImage($event.target.files[0])" />
                    </template>
                    <template v-if="form.file || defaultMedia">
                        <Label for="title">Image Title</Label>
                        <Input type="text" v-model="form.title" id="title" name="title" placeholder="Title" />
                        <Label for="alt">Alt Text</Label>
                        <Input type="text" v-model="form.alt" id="alt" name="alt" placeholder="Alt Text" />
                        <ul>
                            <li class="text-sm text-muted-foreground mt-1">File Type: {{ defaultMedia ? defaultMedia.mime_type : form.file?.type }}</li>
                            <li class="text-sm text-muted-foreground">File Size: {{ defaultMedia ? (defaultMedia.size / 1024).toFixed(2) + ' KB' : ((form.file?.size || 0) / 1024).toFixed(2) + ' KB' }}</li>
                            <li class="text-sm text-muted-foreground">Uploaded At: {{ defaultMedia ? defaultMedia.created_at : new Date(form.file?.lastModified || 0).toLocaleString() }}</li>
                        </ul>
                    </template>
                </section>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="submit" class="capitalize">{{ defaultMedia ? 'Update' : 'Upload' }}</Button>
                    </DialogClose>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>

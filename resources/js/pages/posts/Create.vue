<script>
export default {
    breadcrumbs: [
        { title: 'Posts', href: route('posts.index') },
        { title: 'Create Post', href: route('posts.create') }
    ]
};
</script>

<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { QuillEditor } from '@vueup/vue-quill';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { ref, computed, onMounted, reactive } from 'vue';
import MediaSelector from '@/components/MediaSelector.vue';
import ImageOverview from '@/components/ImageOverview.vue';
import { makeUrl } from '@/composables/useUrl';
import { slugify, makeUrl as makeUrlAccordingMainOrigin } from '@/composables/useSlug';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import InlineHeader from '@/components/InlineHeader.vue';
import SelectCategories from '@/components/SelectCategories.vue';
import { translateToEn } from '@/composables/useTranslation';
import { watchDebounced } from '@vueuse/core';

defineOptions({ inheritAttrs: false })
/** Simple slugify: lower, remove invalid chars, replace spaces with -, collapse dashes */
const props = defineProps(['canCreatePost'])

const tempOpen = ref(false),
    slugManuallyEdited = ref(false),
    tempImage = ref(null),
    tools = reactive({
        container: [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            ['link', 'video'],
            ['imageUploader'],          // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'align': [] }]                               // remove formatting button
        ],
        handlers: {
            imageUploader: () => tempOpen.value = true
        }
    }),
    editor = ref(null);

const form = useForm({
  title: '',
  slug: '',
  meta_description: '',
  content: '',
  status: 'draft',
  featured_image: null,
  categories: []
});

function onSlugInput() {
  slugManuallyEdited.value = true;
  form.slug = slugify(form.slug);
}

const fullUrl = computed(() => makeUrlAccordingMainOrigin(form.slug));

function submit() {
  form.post(route('posts.store'), {
    onBefore: (request) => {
        if (request.data.featured_image) {
            request.data.featured_image = request.data.featured_image.id
        }
    },
    onSuccess: () => {
      slugManuallyEdited.value = false;
    },
  });
}

function putImage(event) {
    if(!event?.path) return;
    tempImage.value = event;
    const url = makeUrl(event.path);
    const img = `<img src="${url}" alt="${event.alt}" title="${event.title}">`;
    const quill = editor.value.getQuill();
    quill.pasteHTML(quill.getSelection().index, img);
    tempImage.value = null;
}

watchDebounced(() => form.title, async (val) => {
  let translatedTitle = await translateToEn(val)
  translatedTitle = translatedTitle?.translated
  const generated = slugify(translatedTitle || val);
  if (!slugManuallyEdited.value) {
    form.slug = generated;
  }
}, { debounce: 100 });

onMounted(() => {
    let button = document.querySelector('.ql-imageUploader');
    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#646c72"><path d="M480-480ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h320v80H200v560h560v-280h80v280q0 33-23.5 56.5T760-120H200Zm40-160h480L570-480 450-320l-90-120-120 160Zm480-280v-167l-64 63-56-56 160-160 160 160-56 56-64-63v167h-80Z"/></svg>';
})
</script>

<template>
  <Head title="Create Post" />
  <div class="w-full mx-auto p-6 rounded-md shadow-sm">
    <InlineHeader title="Create Posts" :link="{ href: route('posts.index'), text: 'Back to posts' }" />
    <form @submit.prevent="submit" class="space-y-4">

        <!-- SEO fields: only meta description now -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 space-y-4">
                <div class="space-y-2">
                  <Label for="title">Title</Label>
                  <Input id="title" v-model="form.title" placeholder="Post title" required />
                  <InputError :message="form.errors.title" />
                </div>

                <div>
                  <Label for="slug">Slug</Label>
                  <Input id="slug" v-model="form.slug" @input="onSlugInput" placeholder="post-slug" required />
                  <p class="text-sm text-muted-foreground mt-1">URL preview: <span class="font-mono">{{ fullUrl }}</span></p>
                  <InputError :message="form.errors.slug" />
                </div>
                <div>
                  <Label for="meta_description">Meta description</Label>
                  <textarea id="meta_description" v-model="form.meta_description" class="w-full rounded outline-0 p-2 mt-1 bg-transparent border" :class="{ 'border-destructive': (form.meta_description || '').length > 200 }" rows="3" placeholder="Meta description for SEO (max ~160 chars)"></textarea>
                  <p class="text-xs text-muted-foreground mt-1">{{ (form.meta_description || '').length }} / 200</p>
                  <InputError :message="form.errors.meta_description" />
                </div>
                <div class="w-full">
                  <Label for="categories">Categories</Label>
                  <SelectCategories v-model="form.categories"/>                   
                </div>
            </div>
           <div>
                <Label for="meta_description">Featured Image</Label>
                <div class="mt-1 w-full min-h-64 border border-dashed rounded flex items-center justify-center">
                    <ImageOverview v-if="form.featured_image" :item="form.featured_image" :select="false" class="w-full [&>img]:h-auto" @removed="form.featured_image = null"/>
                    <MediaSelector v-else v-model="form.featured_image"/>
                </div>
                <InputError :message="form.errors.meta_description" />
           </div>
       </div>

      <!-- Rich text editor toolbar + editor -->
      <div>
        <Label for="content" class="mb-2">Content</Label>
        <MediaSelector :model-value="tempImage" @update:model-value="putImage" v-model:open="tempOpen">
            <template #trigger><span></span></template>
        </MediaSelector>
        <QuillEditor v-model:content="form.content" ref="editor" content-type="html" placeholder="Post content" :toolbar="tools" theme="snow"/>
        <InputError :message="form.errors.content" />
      </div>

      <div class="flex items-center justify-between">
        <Select v-model="form.status" id="status" name="status" required default-value="draft" >
            <SelectTrigger class="w-[180px]">
                <SelectValue placeholder="Status" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="draft">Draft</SelectItem>
                <SelectItem value="published">{{ `${ canCreatePost ? '' : 'Request To ' }Publish` }}</SelectItem>
            </SelectContent>
        </Select>
        <Button type="submit" :disabled="form.processing">
          <span v-if="form.processing">Creating...</span>
          <span v-else>Create post</span>
        </Button>
      </div>
    </form>
  </div>
</template>

<style>
@import '@vueup/vue-quill/dist/vue-quill.snow.css';
@reference "../../../css/app.css";

.ql-editor {
  @apply prose max-w-none min-h-[200px] p-4 dark:prose-invert;
}
</style>

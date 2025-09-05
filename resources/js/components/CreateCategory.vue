<script setup lang="ts">
import { Input } from '@/components/ui/input'; 
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button'; 
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { slugify, makeUrl } from '@/composables/useSlug';
import { Category } from '@/types';

const props = withDefaults(
    defineProps<{
        default?: Category | null
    }>(), {
        default: null
    }
)
const emit = defineEmits(['submitted'])
const form = useForm({
    name: '',
    bangla_name: '',
    slug: ''
})
if(props.default) {
    form.name = props.default.name
    form.slug = props.default.slug
    form.bangla_name = props.default.bangla_name
}
const slugManuallyEdited = ref(false)

function onSlugInput(value: string) {
  slugManuallyEdited.value = true;
  form.slug = slugify(value);
}

function updateName(value: string) {
    form.name = value;
    if (!slugManuallyEdited.value) {
        form.slug = slugify(value);
    }
}

const submit = () => {
    form[ props.default ? 'put' : 'post'](props.default ? route('categories.update', props.default.id) : route('categories.store'), {
        onFinish: () => {
            form.name = '';
            form.slug = '';
            form.bangla_name = '';
            slugManuallyEdited.value = false;
            emit('submitted');
        }
    });
}
</script>

<template>
    <div v-bind="$attrs">
        <form class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:items-end justify-center" @submit.prevent="submit">
            <div class="space-y-2 w-full">
                <Label for="name">Category Name</Label>
                <Input :model-value="form.name" @update:model-value="updateName($event as string)" title="name" type="text" id="name" placeholder="Category Name"/>
            </div>
            <div class="space-y-2 w-full">
                <Label for="name">Category Bangla Name</Label>
                <Input v-model="form.bangla_name" title="name" type="text" id="name" placeholder="Category Name"/>
            </div>
            <div class="space-y-2 w-full">
                <Label for="slug">Category Slug</Label>
                <Input :model-value="form.slug" @update:model-value="onSlugInput($event as string)" title="slug" type="text" id="slug" placeholder="Category Slug"/>
            </div>
            <Button type="submit">{{ props.default ? 'Edit' : 'Create' }}</Button>
        </form>
        <span class="text-sm text-muted-foreground">{{ makeUrl(form.slug) }}</span>
    </div>
</template>
<script lang="ts">
export default {
    breadcrumbs: [
        { title: 'Categories', href: route('categories.index') },
    ]
};
</script>

<script setup lang="ts">
import InlineHeader from '@/components/InlineHeader.vue';
import { Button } from '@/components/ui/button';
import { Paginator } from '@/components/ui/pagination';
import { makeUrl } from '@/composables/useSlug';
import { Category, Paginated } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue';

const CreateCategory = defineAsyncComponent(() => import('@/components/CreateCategory.vue'));

defineProps<{
    categories: Paginated<Category>,
    can: {
        create: boolean,
        delete: boolean,
        update: boolean
    }
}>();

defineOptions({
    inheritAttrs: false,
});

const open = ref<boolean>(false),
    defaultValue = ref<Category | null>(null),
    deleting = ref<number | null>(null),
    editing = ref<boolean | null>(false),
    text = ref<string>('Create Category');

const edit = (category: Category) => {
    editing.value = true;
    open.value = true;
    defaultValue.value = category;
    text.value = 'Exit Edit Mode';
}

const destroy = (id: number) => {
    if (!confirm('Do you really want to delete this category? It will be permanently deleted. You will be not able to restore it.')) return;
    deleting.value = id;
    router.delete(route('categories.destroy', id), {
        preserveState: true,
        onFinish: () => {
            deleting.value = null;
        }
    })
}

const toggle = () => {
    open.value = !open.value
    if(editing.value && !open.value) {
        text.value = 'Create Category'
        defaultValue.value = null
        editing.value = false
    }
}
</script>

<template>
    <Head title="Categories" />
    <div class="p-6">
        <InlineHeader title="Categories" as="button" :link="can.create ? { href: null,  text: text } : null" @click="toggle" class="hover:underline underline-offset-4 cursor-pointer"/>
        <div class="bg-background rounded-md shadow-sm overflow-hidden space-y-4">
            <CreateCategory v-if="open && can.create" :default="defaultValue" @submitted="toggle"/>
            <table class="min-w-full divide-y">
                <thead class="bg-primary-foreground">
                    <tr>
                        <th v-for="header in ['Name', 'URL', 'Created At', 'Updated At', 'Actions']" class="px-4 py-3 text-left text-sm font-medium">{{ header }}</th>
                    </tr>
                </thead>
                <tbody class="bg-accent divide-y">
                    <tr v-for="category in categories.data" :key="category.id">
                        <td class="px-4 py-3 text-sm">
                            {{  category.bangla_name  }} ({{ category.name }})
                        </td>
                        <td class="px-4 py-3 text-sm">{{ makeUrl(category.slug) }}</td>
                        <td class="px-4 py-3 text-sm">{{ category.created_at }}</td>
                        <td class="px-4 py-3 text-sm">{{ category.updated_at }}</td>
                        <td class="px-4 py-3 text-sm">
                           <Button v-if="can.update" variant="link" size="sm" @click="edit(category)" :disabled="editing">
                                <span v-if="editing">Editing</span>
                                <span v-else>Edit</span>
                            </Button>
                            <Button v-if="can.delete" variant="destructive" size="sm" @click="destroy(category.id)" :disabled="deleting === category.id">
                                <span v-if="deleting === category.id">Deleting...</span>
                                <span v-else>Delete</span>
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Paginator :pagiantion-data="categories" base-route-name="categories.index"/>
        </div>
    </div>
</template>

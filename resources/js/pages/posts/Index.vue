<script lang="ts">
export default {
    breadcrumbs: [
        {
            title: 'Posts',
            href: '/posts',
        }
    ]
};
</script>

<script setup lang="ts">
import { ref } from 'vue';
import { Paginated, User } from '@/types';
import { router, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { makeUrl } from '@/composables/useSlug';
import UserInfo from '@/components/UserInfo.vue';
import TextLink from '@/components/TextLink.vue';
import InlineHeader from '@/components/InlineHeader.vue';
import { Paginator } from '@/components/ui/pagination';

interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt?: string | null;
  content?: string | null;
  status: 'draft' | 'published' | 'pending' | 'rejected';
  published_at?: string | null;
  author: User;
  can_delete: boolean;
}
// typed props
defineProps<{ posts: Paginated<Post>, canChangeStatus: boolean }>();
const deleting = ref<number | null>(null);

function destroy(id: number) {
  if (!confirm('Do you really want to delete this post? It will be permanently deleted. You will be not able to restore it.')) return;
  deleting.value = id;
  router.delete(route('posts.destroy', id), {
    preserveState: true,
    onFinish: () => {
      deleting.value = null;
    }
  })
}
</script>

<template>
    <Head title="Posts" />
    <div class="p-6">
        <InlineHeader title="Posts" :link="{ href: route('posts.create'), text: 'Create Post' }" />
        <div class="bg-background rounded-md shadow-sm overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-primary-foreground">
                <tr>
                    <th v-for="header in ['Title', 'Status', 'Author', 'Published', 'Actions']" class="px-4 py-3 text-left text-sm font-medium">{{ header }}</th>
                </tr>
                </thead>
                <tbody class="bg-accent divide-y">
                <tr v-for="post in posts.data" :key="post.id">
                    <td class="px-4 py-3 text-sm">
                        {{ post.title }}
                        <p class="text-xs text-muted-foreground mt-1">Url: {{ makeUrl(post.slug) }}</p>
                        <template v-if="(post.status === 'pending') && canChangeStatus">
                            <TextLink method="post" :href="route('posts.change_status', post.id)" :data="{ status: 'approve' }">Approve</TextLink>
                            <TextLink method="post" :href="route('posts.change_status', post.id)" :data="{ status: 'reject' }" class="mx-2 hover:text-destructive">Reject</TextLink>
                        </template>
                    </td>
                    <td class="px-4 py-3 text-sm capitalize">{{ post.status === 'rejected' ? 'Admin ' : '' }}{{ post.status }}{{ (post.status === 'pending') ? ' (Waiting for Admin Approval)' : '' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <UserInfo :user="post.author" :show-icon="false" :show-email="true"/>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ post.published_at || '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right space-x-4">
                        <TextLink :href="route('posts.edit', post.id)">Edit</TextLink>
                        <Button v-if="post.can_delete" variant="destructive" size="sm" @click="destroy(post.id)" :disabled="deleting===post.id">
                            <span v-if="deleting===post.id">Deleting...</span>
                            <span v-else>Delete</span>
                        </Button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- basic pagination UI if provided by backend -->
        <Paginator v-if="posts.links" class="mt-4" :pagiantion-data="posts" base-route-name="posts.index" />
    </div>
</template>

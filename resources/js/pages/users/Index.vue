<script lang="ts">
export default {
    breadcrumbs: [
        {
            title: 'Users',
            href: route('users.index'),
        }
    ]
};
</script>

<script setup lang="ts">
import { defineAsyncComponent, ref } from 'vue';
import { router, Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { User, Paginated } from '@/types';
import InlineHeader from '@/components/InlineHeader.vue';

const Paginator =  defineAsyncComponent(() => import("@/components/ui/pagination/Paginator.vue"))

defineOptions({ inheritAttrs: false })
// typed props - expose `users` variable to the template
const { users } = defineProps<{ users: Paginated<User> }>();
const deleting = ref<number | null>(null);

function destroy(id: number) {
    if (!confirm('Delete this user?')) return;
    deleting.value = id;
    router.delete(route('users.destroy', { 'user': id }), {
        onFinish: () => {
            deleting.value = null;
        },
        preserveState: false
    })
}
</script>

<template>
    <Head title="Users" />
    <div class="p-6">
        <InlineHeader title="Users" />
        <div class="bg-background rounded-md shadow-sm overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-primary-foreground">
                    <tr>
                        <th v-for="header in ['Name', 'Email', 'Role', 'Email Verified At', 'Registerd At', 'Actions']" class="px-4 py-3 text-left text-sm font-medium">{{ header }}</th>
                    </tr>
                </thead>
                <tbody class="bg-accent divide-y">
                <tr v-for="user in users.data" :key="user.id">
                    <td class="px-4 py-3 text-sm">{{ user.name }}</td>
                    <td class="px-4 py-3 text-sm">{{ user.email }}</td>
                    <td class="px-4 py-3 text-sm">{{ user.role }}</td>
                    <td class="px-4 py-3 text-sm" :class="{ 'text-destructive': !user.email_verified_at }">{{ user.email_verified_at ?? 'Not Verified Yet' }}</td>
                    <td class="px-4 py-3 text-sm">{{ user.created_at }}</td>
                    <td class="px-4 py-3 text-sm text-right">
                        <Button v-if="user.id !== $page.props.auth.user.id" variant="destructive" size="sm" @click="destroy(user.id)" :disabled="(deleting===user.id) || (user.role === $page.props.auth.user.role)">
                            <span v-if="deleting===user.id">Deleting...</span>
                            <span v-else>Delete</span>
                        </Button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- basic pagination UI if provided by backend -->
        <Paginator v-if="users.total > users.per_page"  :pagiantion-data="users" base-route-name="users.index" class="mt-4"/>
    </div>
</template>


<script setup lang="ts">
import { Media } from '@/types';
import { router } from '@inertiajs/vue3';
import { defineAsyncComponent } from 'vue';
import { makeUrl } from '@/composables/useUrl';
import { Button } from '@/components/ui/button';
import { CircleCheck, Trash } from 'lucide-vue-next';

const UserInfo = defineAsyncComponent(() => import('@/components/UserInfo.vue'));

defineProps({
    item: {
        required: true,
        type: Object as () => Media
    },
    selection: { required: false, type: Object as () => Media | null },
    select: { required: true, type: Boolean }
})

const emit = defineEmits(['click', 'removed'])

const remove = (item: Media) => {
    if (!confirm('Do you really want to delete this file? It will be permanently deleted. You will be not able to restore it.')) return;
    router.delete(route('media.destroy', item.id), {
        onFinish: () => emit('removed', item)
    })
}
</script>
<template>
    <div class="border rounded-lg overflow-hidden relative">
        <img :src="makeUrl(item.path)" @click="emit('click')" :alt="item.alt" class="w-full h-32 object-contain cursor-pointer hover:opacity-60 transition-opacity duration-300" />
        <CircleCheck v-if="select && (item.id === selection?.id)" class="w-5 h-5 absolute top-0 left-0 text-primary fill-primary-color"></CircleCheck>
        <div class="p-2 space-y-2">
            <h2 class="text-sm font-medium text-primary truncate">{{ item.title }}</h2>
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs text-muted-foreground ">{{ (item.size / 1024).toFixed(2) }}KB</span>
                    <span class="text-xs text-muted-foreground">{{ item.mime_type }}</span>
                </div>
                <Button v-if="item.can_delete" @click="remove(item)" variant="destructive" size="sm" type="button"> <Trash class="w-4 h-4" /> </Button>
            </div>
        </div>
        <div v-if="item.author" class="flex items-center p-2 pt-0 text-xs text-muted-foreground space-x-2">
            <span>by</span>
            <UserInfo :user="item.author" :show-icon="false" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger, DialogClose } from '@/components/ui/dialog';
import { onMounted, ref, type Ref } from 'vue';
import CheckMark from '@/components/CheckMark.vue';
import axios from 'axios';
import { Media } from '@/types';
import { router } from '@inertiajs/vue3'

interface Post {
    id: number;
    title: string;
    featured_image: Media | null
}

interface Position {
    id: number;
    name: string;
    post: Post;
}

const props = defineProps<{
    post: Post;
}>();

const selection : Ref<string|null> = ref(null);
const positions = ref<Position[]>([]);

const getPositions = () => {
    axios.get(route('position.index')).then(res => {
        positions.value = res.data
        positions.value.forEach(position => {
            if(position.post?.id === props.post.id) {
                selection.value = position.name;
            }
        })
    }).catch(err => {
        console.log(err)
    })
}

const remove = () => {
    selection.value = null;
    axios.delete(route('position.destroy', { 'position': props.post.id })).then(res => {
        getPositions();
    }).catch(err => {
        console.log(err)
    })
}

const select = () => {
    if(!selection.value) return;
    let selectedPosition = null;
    for (const key in positions.value) {
        const position = positions.value[key];
        if(position.name === selection.value) {
            selectedPosition = position.id
        }   
    }
    router.put(route('position.update', { 'position': selectedPosition }), { postId: props.post.id }, {
        preserveScroll: true,
        preserveState: true
    })
}

const getSelectedPositionName = () => {
    if(!selection.value) return 'Select Position';
    return `Position ${selection.value.replaceAll('-', ' ')} Selected. Click to change`;
}

const getImageUrl = (positionName: string) => {
    let url = location.origin + '/';
    for (const key in positions.value) {
        const position = positions.value[key];
        if(position.name === positionName) {
            url += position.post?.featured_image?.path || ''
        }   
    }
    return url
}

const getPostTitle = (positionName: string) => {
    let title = '';
    for (const key in positions.value) {
        const position = positions.value[key];
        if(position.name === positionName) {
            title = position.post?.title || ''
        }   
    }
    return title
}

onMounted(() => {
    getPositions();
})
</script>

<template>
    <Dialog @update:open="getPositions">
        <DialogTrigger>
            <slot name="trigger">
                <Button type="button" variant="secondary" class="inline-flex items-center capitalize">
                    {{ getSelectedPositionName() }}
                </Button>
            </slot>
        </DialogTrigger>
        <DialogContent as="div" class="!max-w-[90dvw] max-h-[80dvh] h-full overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Select Position</DialogTitle>
                <DialogDescription class="w-full flex justify-between items-center">
                    <p>Select Position for this post.</p>
                    <Button type="button" variant="destructive" @click="remove">Remove Selection</Button>
                </DialogDescription>
            </DialogHeader>
            <div class="container mx-auto px-4 py-6 grid grid-cols-12 gap-6">
                <!-- Left Sidebar -->
                <div class="col-span-3 space-y-6">
                    <div v-for="i in 4" :key="'left-'+i" class="space-y-4 pb-4 relative cursor-pointer group" @click="selection = 'left-'+i">
                        <CheckMark v-if="selection === 'left-'+i" />
                        <div class="flex space-x-3">
                            <div class="flex-1 space-y-2">
                                <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4">
                                    Left Sidebar {{ i }}
                                </div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 rounded w-3/4  transition-all duration-300 text-xs">
                                    {{ getPostTitle('left-'+i) }}
                                </div>
                            </div>
                            <div v-if="i <= 2" class="w-24 h-16 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 rounded  transition-all duration-300 bg-contain bg-center" :style="{ backgroundImage: `url('${getImageUrl('left-'+i)}')` }"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 rounded w-full transition-all duration-300"></div>
                            <div class="h-2 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 rounded w-3/4 transition-all duration-300"></div>
                        </div>
                    </div>
                    <div>
                        <div v-for="i in 3" :key="'left-'+(i+4)" class="flex space-x-3 py-4 relative cursor-pointer group" @click="selection = 'left-'+(i + 4)">
                            <CheckMark v-if="selection === 'left-'+(i + 4)" />
                            <div class="flex-1 space-y-2">
                                <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300rounded w-3/4">
                                    Left Sidebar {{ i + 4 }}
                                </div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-1/2 text-xs">
                                    {{ getPostTitle('left-'+(i + 4)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Featured Area -->
                <div class="col-span-6 space-y-6">
                    <!-- Featured Post -->
                    <div class="flex gap-4">
                        <div class="group cursor-pointer w-full h-72 bg-gray-200 dark:bg-gray-700 hover:bg-gray-400 hover:dark:bg-gray-500 transition-all duration-300 rounded flex flex-col justify-end p-4 space-y-4 relative bg-center bg-cover" @click="selection = 'featured'"  :style="{ backgroundImage: `url('${getImageUrl('featured')}')` }">
                            <CheckMark v-if="selection === 'featured'" />
                            <div class="px-1 bg-gray-400 dark:bg-gray-600 group-hover:bg-gray-500 transition-all duration-300 rounded w-3/4">
                                Featured
                            </div>
                            <div class="h-5 bg-gray-400 dark:bg-gray-600 rounded w-2/3 text-xs">
                                {{ getPostTitle('featured') }}
                            </div>
                            <div class="h-4 bg-gray-400 dark:bg-gray-600 rounded w-1/2"></div>
                        </div>
                        <div class="space-y-3 w-64 relative group cursor-pointer" @click="selection = 'featured-2'">
                            <CheckMark v-if="selection === 'featured-2'" />
                            <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded bg-center bg-cover" :style="{ backgroundImage: `url('${getImageUrl('featured-2')}')` }"></div>
                            <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4">
                                Featured 2
                            </div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-2/3 text-xs">
                                {{ getPostTitle('featured-2') }}
                            </div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-1/2"></div>
                        </div>
                    </div>

                    <!-- 2-column posts under main -->
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="i in 4" :key="'main-'+i" class="space-y-2 relative cursor-pointer group" @click="selection = 'main-'+i">
                            <CheckMark v-if="selection === 'main-'+i" />
                            <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded bg-center bg-contain" :style="{ backgroundImage: `url('${getImageUrl('main-'+i)}')` }"></div>
                            <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4">
                                Main Post {{ i }}
                            </div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-1/2 text-xs">
                                {{ getPostTitle('main-'+i) }}
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-full"></div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div v-for="i in 3" :key="'secondary-'+i" class="space-y-2 relative cursor-pointer group" @click="selection = 'secondary-'+i">
                            <CheckMark v-if="selection === 'secondary-'+i" />
                            <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded bg-contain bg-center" :style="{ backgroundImage: `url('${getImageUrl('secondary-'+i)}')` }"></div>
                            <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4">
                                Secondery Post {{ i }}
                            </div>
                            <div class="h-5 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-full text-xs">
                                {{ getPostTitle('secondary-'+i) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="col-span-3 space-y-4">
                    <div class="w-full h-48 bg-gray-900 dark:bg-gray-800 rounded text-xl text-center text-white">Ad</div>
                    <div v-for="i in 3" :key="'right-'+i" class="space-y-2 relative group cursor-pointer" @click="selection = 'right-'+i">
                        <CheckMark v-if="selection === 'right-'+i" />
                        <div class="w-full h-28 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded bg-contain bg-center" :style="{ backgroundImage: `url('${getImageUrl('right-'+i)}')` }"></div>
                        <div class="px-1 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-3/4">
                            Right Sidebar {{ i }}
                        </div>
                        <div class="h-5 bg-gray-200 dark:bg-gray-700 group-hover:bg-gray-400 group-hover:dark:bg-gray-500 transition-all duration-300 rounded w-full text-xs">
                            {{ getPostTitle('right-'+i) }}
                        </div>
                    </div>
                    <!-- Ad placeholder -->
                    <div class="w-full h-48 bg-gray-900 dark:bg-gray-800 rounded text-xl text-center text-white">Ad</div>
                </div>
            </div>
            <DialogFooter>
                <DialogClose>
                    <Button type="button" @click="select" :disabled="!selection">Select</Button>
                </DialogClose>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

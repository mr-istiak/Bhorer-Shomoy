<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import { VNode } from 'vue';
import Heading from '@/components/Heading.vue';
const props = withDefaults(
    defineProps<{ 
        title: string;
        description?: string;
        link?: {
            href: string | null;
            text: string;
        } | null ;
        class? : string;
        as?: string | VNode
    }>(), 
    { link : null, as: 'span' }
)

defineEmits(['click'])
</script>
<template>
    <div class="flex items-center justify-between mb-6">
        <Heading :title="title" :description="description"/>
        <!-- <h1 class="text-2xl font-semibold text-primary">{{ title }}</h1> -->
        <component :is="link?.href ? TextLink : as" v-if="link" :href="link.href" :class="['inline-flex items-center', props.class ]" v-bind="$attrs" @click="$emit('click', $event)">{{ link.text }}</component>
    </div>
</template>
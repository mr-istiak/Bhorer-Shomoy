<script setup lang="ts">
import { AlertCircle, XCircle } from "lucide-vue-next"
import type { BreadcrumbItemType } from '@/types';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { reactive, watch } from "vue";

defineOptions({
    inheritAttrs: true,
});

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    messages: {
        success?: string | null | string[];
        warning?: string | null | string[];
        info?: string | null | string[];
    };
    errors?: Record<string, string[]>; // Validation errors
}
const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => []
});
const flash = reactive<{
    errors: boolean;
    success: boolean;
    warning: boolean;
    info: boolean;
}>({
    errors: false,
    success: false,
    warning: false,
    info: false,
});

watch(() => props.errors, (newErrors) => {
    if (Object.keys(newErrors || {}).length > 0) {
        flash.errors = true;
    } else {
        flash.errors = false;
    }
}, { immediate: true });
watch(() => props.messages, (newMessages) => {
    flash.success = !!newMessages.success;
    flash.warning = !!newMessages.warning;
    flash.info = !!newMessages.info;
}, { immediate: true, deep: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" v-bind="$attrs">
        <slot />
    </AppLayout>

    <div class="fixed bottom-4 right-4 max-w-74 z-50 space-y-3">
         <Alert v-if="flash.errors" variant="destructive">
            <AlertCircle class="w-4 h-4 stroke-current" />
            <AlertTitle class="capitalize" title="Error">Error</AlertTitle>
            <XCircle class="absolute top-2 right-2 cursor-pointer !w-5 !h-5" @click="flash.errors = false" />
            <AlertDescription>
                <div v-for="(m, i) in props.errors" :key="i">{{ Object.keys(props.errors || {}).length > 1 ? '# ': '' }}{{ m }}</div>
            </AlertDescription>
        </Alert>
        <template v-for="messageType in ['success', 'warning', 'info']" :key="messageType">
            <Alert v-if="flash[messageType as keyof typeof props.messages]" variant="default" :class="{
                    'text-success': messageType === 'success',
                    'text-warning': messageType === 'warning',
                    'text-info': messageType === 'info',
                }">
                <AlertCircle class="w-4 h-4 stroke-current" />
                <AlertTitle class="capitalize" :title="messageType">{{ messageType }}</AlertTitle>
                <XCircle class="absolute top-2 right-2 cursor-pointer !w-5 !h-5" @click="flash[messageType as keyof typeof flash] = false" />
                <AlertDescription>
                    <template v-if="Array.isArray(props.messages[messageType as keyof typeof props.messages])">
                        <div v-for="(m, i) in props.messages[messageType as keyof typeof props.messages]" :key="i"># {{ m }}</div>
                    </template>
                    <template v-else>{{ props.messages[messageType as keyof typeof props.messages] }}</template>
                </AlertDescription>
            </Alert>
        </template>
    </div>
</template>

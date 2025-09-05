<script setup lang="ts">
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { useVModel } from '@vueuse/core'

const props = defineProps<{
    id?: string;
    name?: string;
    required?: boolean;
    tabindex?: number;
    autocomplete?: string;
    placeholder?: string;
    defaultValue?: string | number;
    modelValue?: string | number;
}>();
const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

const show = ref(false);

function toggle() {
    show.value = !show.value;
}
</script>

<template>
    <div class="relative">
        <Input
            :id="props.id"
            :name="props.name"
            :type="show ? 'text' : 'password'"
            :required="props.required"
            :tabindex="props.tabindex"
            :autocomplete="props.autocomplete"
            :placeholder="props.placeholder"
            v-model="modelValue"
            class="pr-10"
        />
        <Button
            type="button"
            variant="ghost"
            size="icon"
            class="absolute right-2 top-1/2 -translate-y-1/2 p-1"
            tabindex="-1"
            @click="toggle"
        >
            <span v-if="show" aria-label="Hide password">🙈</span>
            <span v-else aria-label="Show password">👁️</span>
        </Button>
    </div>
</template>

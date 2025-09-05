<script setup lang="ts">
import { ref } from "vue";
import axios from 'axios'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"

const data = ref<{ name: string; bangla_name: string; id: number }[]>([]);
axios.get(route('categories.index', { select: true })).then(res => {
    data.value = res.data
})
</script>

<template>
    <Select v-bind="$attrs" id="categories" name="categories" multiple>
        <SelectTrigger class="w-full">
            <SelectValue placeholder="Select Categories" />
        </SelectTrigger>
        <SelectContent>
            <SelectItem v-for="category in data" :key="category.id" :value="Number(category.id)">
                {{ category.bangla_name }} ({{ category.name }})
            </SelectItem>
        </SelectContent>
    </Select>
</template>
<script setup lang="ts">
import { BasePagination, Paginated } from "@/types";
import { router } from '@inertiajs/vue3'
import { Pagination, PaginationContent, PaginationEllipsis, PaginationItem, PaginationNext, PaginationPrevious } from "@/components/ui/pagination"
const props = defineProps<{
    pagiantionData: Paginated<BasePagination>,
    baseRouteName: string
}>()

function goToPage(page: number) {
  if (!page || page < 1) return;
  if (page === props.pagiantionData.current_page) return;
  // navigate to the given page while preserving state
  router.get(route(props.baseRouteName, { page }), { preserveState: true, replace: true });
}
</script>
<template>
    <Pagination v-slot="{ page }" :items-per-page="pagiantionData.per_page" :total="pagiantionData.total" :default-page="pagiantionData.current_page" v-bind="$attrs">
        <PaginationContent v-slot="{ items }">
            <!-- Previous -->
            <PaginationPrevious @click="goToPage(Math.max(1, page - 1))" />

            <!-- page items -->
            <template v-for="(item, index) in items" :key="index">
                <PaginationItem
                    v-if="item.type === 'page'"
                    :value="item.value"
                    :is-active="item.value === page"
                    @click="goToPage(item.value)"
                >
                    {{ item.value }}
                </PaginationItem>
            </template>

            <!-- ellipsis (keeps existing behaviour) -->
            <PaginationEllipsis :index="4" />

            <!-- Next -->
            <PaginationNext @click="goToPage(Math.min(pagiantionData.last_page, page + 1))" />
        </PaginationContent>
    </Pagination>
</template>
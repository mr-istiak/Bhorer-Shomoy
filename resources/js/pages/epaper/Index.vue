<script lang="ts">
export default {
    breadcrumbs: [
        {
            title: 'Epapers',
            href: route('epapers.index'),
        }
    ]
};
</script>

<script setup lang="ts">
import { Button } from "@/components/ui/button"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog"
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Paginator } from '@/components/ui/pagination';
import { Epaper, Paginated } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { getBanglaWeekDay, getEnglishDateInBangla, getGragorianToBongabdoDate } from '@/../../../bhorershomoy/src/js/date'
import TextLink from "@/components/TextLink.vue";

defineProps<{
    epapers: Paginated<Epaper>
}>()

const form = useForm({
    title: '',
    pdf: null
})

const genareteTitle = () => {
    form.title = `${getBanglaWeekDay()}, ${getGragorianToBongabdoDate()} বঙ্গাব্দ (${getEnglishDateInBangla().join()})`
}

const submit = () => {
    form.post(route('epapers.store'), {
        preserveScroll: true
    })
}
</script>

<template>
    <Head title="Epapers" />
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Epapers</h1>
            <Dialog @update:open="genareteTitle">
                <DialogTrigger>
                    <Button variant="link" as="span">Upload Epaper</Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Upload Epaper</DialogTitle>
                        <DialogDescription>
                            Upload your epaper here
                        </DialogDescription>
                    </DialogHeader>
                    <Label for="title">Title</Label>                    
                    <Input v-model="form.title" id="title" type="text" placeholder="Paper Title" class="border p-2 w-full" />
                    <Label for="pdf">PDF</Label>
                    <Input @change="form.pdf = $event.target.files[0]" id="pdf" type="file" accept="application/pdf" />
                    <DialogFooter>
                        <DialogClose>
                            <Button type="submit" @click="submit" variant="default">
                                Upload
                            </Button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
        <div class="bg-background rounded-md shadow-sm overflow-hidden">
            <table class="min-w-full divide-y">
                <thead class="bg-primary-foreground">
                <tr>
                    <th v-for="header in ['Title', 'Published At', 'Created At', 'Actions']" class="px-4 py-3 text-left text-sm font-medium">{{ header }}</th>
                </tr>
                </thead>
                <tbody class="bg-accent divide-y">
                <tr v-for="epaper in epapers.data" :key="epaper.id">
                    <td class="px-4 py-3 text-sm">
                        {{ epaper.title }}
                    </td>
                    <td class="px-4 py-3 text-sm capitalize">{{ epaper.published_at || '—' }}</td>
                    <td class="px-4 py-3 text-sm">{{ epaper.created_at }}</td>
                    <td class="px-4 py-3 text-sm text-right space-x-4">
                        <TextLink :href="route('epapers.edit', epaper.id)">Edit</TextLink>
                        <!-- <Button v-if="post.can_delete" variant="destructive" size="sm" @click="destroy(post.id)" :disabled="deleting===post.id">
                            <span v-if="deleting===post.id">Deleting...</span>
                            <span v-else>Delete</span>
                        </Button> -->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- basic pagination UI if provided by backend -->
        <Paginator v-if="epapers.total > epapers.per_page" class="mt-4" :pagiantion-data="epapers" base-route-name="epapers.index" />
    </div>
</template>

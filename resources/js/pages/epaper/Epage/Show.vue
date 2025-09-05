<script lang="ts">
export default {
    breadcrumbs: [
        { title: 'Epaper', href: route('epapers.index') },
        { title: 'Configure Epaper', href: '#' },
        { title: 'Epaper Page', href: location.href },
    ]
};
</script>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill';
import { Button } from "@/components/ui/button";
import { makeUrl } from "@/composables/useUrl";
import { LoaderCircle } from "lucide-vue-next";
import { ArticleBox, BoundingBox, Epage } from "@/types"; 
import InlineHeader from '@/components/InlineHeader.vue';
import DragSelection from "@/components/DragSelection.vue";
import { getDocument, GlobalWorkerOptions, } from 'pdfjs-dist';
import { ref, onMounted, useTemplateRef, computed } from "vue";
import { extractTextFromSelection, type Image, intersects, pdfToScreenCoords, screenToPdfCoordsN, TextFragment, transformTextToHtml } from "@/composables/usePDFExtractor";
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger} from "@/components/ui/dialog"
import pdfWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
// Vite-compatible PDF.js worker
GlobalWorkerOptions.workerSrc = pdfWorker
defineOptions({ inheritAttrs: false })
const props = defineProps<{ epage: Epage }>();

// DOM refs
const canvas = ref<HTMLCanvasElement | null>(null);
const overlay = useTemplateRef<InstanceType<typeof DragSelection>>('overlay');
const container = ref<HTMLDivElement | null>(null);
const toolbar = ref<HTMLDivElement>()
// Selection logic
const selections = ref<BoundingBox[]>([]),
    loading = ref(true),
    selectable = ref(false),
    articleBoxes = ref<ArticleBox[]>([]),
    openReview = ref(false),
    selectFromOtherPage = ref(false),
    savedArticleBoxes = useTemplateRef('savedArticleBoxes');

// PDF.js objects — do NOT make reactive
let page: any;
let viewport: any;
let images: Image[] = [];

const scale = computed(() => {
    const unscaledViewport = page.getViewport({ scale: 1 });
    const pdfWidth = unscaledViewport.width;
    const pdfHeight = unscaledViewport.height;
    const containerWidth = container.value!.clientWidth
    container.value!.style.height = `${containerWidth * (pdfHeight / pdfWidth)}px`;
    return Math.min(containerWidth / pdfWidth, container.value!.clientHeight / pdfHeight);
});
const articleId = computed(() => (new URLSearchParams(window.location.search)).get('extending_article'))

const fetchImages = (ctx: CanvasRenderingContext2D) => {
    const originalDrawImage = ctx.drawImage.bind(ctx);
    return function (...args: any[]): void {
        // Get the current transform matrix
        const transform = ctx.getTransform(); // DOMMatrix
        let dx=0, dy=0, dWidth=0, dHeight=0;
        let image = args[0];
        if (args.length === 3) {
            dx = args[1]; dy = args[2];
            dWidth = (image as any).width;
            dHeight = (image as any).height;
        } else if (args.length === 5) {
            dx = args[1]; dy = args[2];
            dWidth = args[3]; dHeight = args[4];
        } else if (args.length === 9) {
            dx = args[5]; dy = args[6];
            dWidth = args[7]; dHeight = args[8];
        }
        // Apply transform to get actual canvas coordinates
        const x = transform.a * dx + transform.c * dy + transform.e;
        const y = transform.b * dx + transform.d * dy + transform.f;
        if (dWidth > 0 && dHeight > 0) {
            images.push({
                rectPdf: screenToPdfCoordsN({ x, y, width: dWidth * transform.a, height: dHeight * transform.d }, viewport.scale, canvas.value!),
                image
            });
        }
        return (originalDrawImage as any)(...args);
    } as typeof ctx.drawImage;
}
// --- Convert screen coordinates to PDF page coordinates ---

async function saveSelections() {
    if (!page) return;
    loading.value = true;
    for (const boundingBox of selections.value) {
        const selectiobRectPdf = screenToPdfCoordsN(boundingBox, viewport.scale, canvas.value!), 
            textFragments: TextFragment[] = await extractTextFromSelection(selectiobRectPdf, page),
            text = transformTextToHtml(textFragments),
            selectedImages: Image[] = images.filter(img => intersects(img.rectPdf, selectiobRectPdf));
        articleBoxes.value.push({
            type: 'text',
            bounding_box: selectiobRectPdf,
            extracted_content: text
        });
        for (const image of selectedImages) {
            articleBoxes.value.push({
                type: 'image',
                bounding_box: image.rectPdf
            });
        }
    }
    loading.value = false;
    openReview.value = true;
}

const cancelSelection = () => {
    if(overlay.value?.cancel()) {
        selectable.value = false;
        articleBoxes.value = [];
    }
}
const toggleSelection = async () => {
    if(!selectable.value) {
        selectable.value = true;
        return;
    }
    await saveSelections();
}

const positionSavedArticleBox = (articleBox: ArticleBox) => {
    const rectScreen =  pdfToScreenCoords(articleBox.bounding_box!, viewport.scale, canvas.value!);
    return { top: rectScreen.y, left: rectScreen.x, width: rectScreen.width, height: rectScreen.height }
}

const renderSavedArticleBoxes = () => {
    for (const key in props.epage.article_boxes) {
        const articleBox = props.epage.article_boxes[key];
        const position = positionSavedArticleBox(articleBox);
        savedArticleBoxes.value![key].style.top = position.top + 'px';
        savedArticleBoxes.value![key].style.left = position.left + 'px';
        savedArticleBoxes.value![key].style.width = position.width + 'px';
        savedArticleBoxes.value![key].style.height = position.height + 'px';
    }
}

const finalizeArticleBoxes = () => {
    router.post(route('epage.articles.store', { epage: props.epage.id }), {
        article_boxes: articleBoxes.value,
        add_selection_from_other_page: selectFromOtherPage.value ?? false,
        article: articleId.value ?? null
    }, { 
        onSuccess: () => {
            cancelSelection();
            renderSavedArticleBoxes();
        }, 
        onFinish: () => articleBoxes.value = [],
        preserveScroll: true,
        preserveState: true
    });
}

const selectFromOtherP = () => {
    selectFromOtherPage.value = true;
    toggleSelection();
}

// --- Mount PDF page ---
onMounted(async () => {
    if (!container.value || !canvas.value || !overlay.value?.canvas) return;
    loading.value = true;
    const pdf = await getDocument(makeUrl(props.epage.epaper.pdf_path)).promise;
    page = await pdf.getPage(props.epage.page_number);
    viewport = page.getViewport({ scale: scale.value });
    // set canvas pixel size (matches PDF viewport)
    canvas.value.width = viewport.width;
    canvas.value.height = viewport.height;
    overlay.value.canvas!.width = viewport.width;
    overlay.value.canvas!.height = viewport.height;
    // render PDF page
    const ctx = canvas.value.getContext("2d")!;
    ctx.drawImage = fetchImages(ctx);
    await page.render({ canvasContext: ctx, viewport, canvas: canvas.value }).promise;
    renderSavedArticleBoxes();
    loading.value = false;
});
</script>

<template>
    <Head title="Epage" />
    <div class="p-6 relative">
        <InlineHeader title="Epaper Page" :link="{ href: route('epapers.edit', { epaper: epage.epaper_id }), text: 'Back to pages' }"/>
        <div class="relative w-full h-[76dvh] space-y-4 overflow-y-auto">
            <div v-if="loading" class="absolute bg-background w-full h-dvh z-50 flex items-center justify-center"><LoaderCircle class="animate-spin w-32 h-32" /></div>
            <div class="bg-primary-foreground w-full flex sticky top-0 z-40 py-2 space-x-2" ref="toolbar">
                <Button variant="ghost" @click="toggleSelection">{{ selectable ? 'Add' : 'Select A' }} Article</Button>
                <Button v-if="selectable" variant="ghost" @click="cancelSelection">Cancel Selection</Button>
                <Button v-if="selectable" variant="ghost" @click="selectFromOtherP">Add Selection From Other Page</Button>
            </div>
            <div ref="container" class="relative w-full overflow-hidden">
                <canvas ref="canvas" class="block"></canvas>
                <DragSelection v-model="selections" ref="overlay" :selectable="selectable" />
                <div v-for="articleBox in epage.article_boxes" :key="articleBox.id" ref="savedArticleBoxes" class="absolute bg-red-500 opacity-20 z-20"></div>
            </div>
        </div>
        <Dialog v-model:open="openReview">
            <DialogTrigger as="div" class="hidden">
                Review
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Review Article</DialogTitle>
                    <DialogDescription>Review Your Selected Article</DialogDescription>
                </DialogHeader>
                <div class="space-y-8 max-h-[80dvh] overflow-y-auto">
                    <template v-for="articleBox in articleBoxes">
                        <QuillEditor v-if="articleBox.type === 'text'" v-model:content="articleBox.extracted_content" 
                        content-type="html" theme="snow" class="h-64"/>
                    </template>
                </div> 
                <DialogFooter>
                    <DialogClose>
                        <Button @click="finalizeArticleBoxes">Finalize Article</Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style>
@import '@vueup/vue-quill/dist/vue-quill.snow.css';
@reference "../../../../css/app.css";

.ql-editor {
  @apply prose max-w-none p-4 dark:prose-invert;
}
</style>
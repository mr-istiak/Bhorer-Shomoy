<script setup lang="ts">
import { BoundingBox } from '@/types';
import { ref } from 'vue';

const props = defineProps<{
    selectable: boolean
}>()
const selections = defineModel<BoundingBox[]>({ required: true });

const canvas = ref<HTMLCanvasElement | null>(null),
    isDragging = ref(false),
    startX = ref(0),
    startY = ref(0),
    currentBox = ref<BoundingBox | null>(null);

const cancel = () => {
    if (!canvas.value) return;
    isDragging.value = false;
    currentBox.value = null;
    startX.value = 0;
    startY.value = 0;
    selections.value = [];
    const ctx = canvas.value.getContext("2d")!;
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);
    return true;
}

defineExpose({
    canvas,
    cancel
})

function drawSelections() {
    if (!canvas.value || !props.selectable) return;
    const ctx = canvas.value.getContext("2d")!;
    ctx.clearRect(0, 0, canvas.value.width, canvas.value.height);

    ctx.strokeStyle = "red";
    ctx.lineWidth = 2;
    ctx.setLineDash([5, 3]);

    [...selections.value, currentBox.value].forEach(box => {
        if (!box) return;
        ctx.strokeRect(box.x, box.y, box.width, box.height);
    });
}


function startDrag(event: MouseEvent) {
    if (!canvas.value || !props.selectable) return;
    isDragging.value = true;
    const rect = canvas.value.getBoundingClientRect();
    startX.value = event.clientX - rect.left;
    startY.value = event.clientY - rect.top;
    currentBox.value = null;
}

function drag(event: MouseEvent) {
    if (!isDragging.value || !canvas.value || !props.selectable) return;
    const rect = canvas.value.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    currentBox.value = {
        x: startX.value,
        y: startY.value,
        width: x - startX.value,
        height: y - startY.value
    };
    drawSelections();
}

function endDrag() {
    if(!props.selectable) return;
    if (currentBox.value) selections.value.push(currentBox.value);
    currentBox.value = null;
    isDragging.value = false;
    drawSelections();
}
</script>

<template>
    <canvas ref="canvas" class="absolute top-0 left-0" :class="{ 'cursor-crosshair': selectable }" @mousedown="startDrag" @mousemove="drag" @mouseup="endDrag" v-bind="$attrs"></canvas>
</template>
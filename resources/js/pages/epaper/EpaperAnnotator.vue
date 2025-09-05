<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Annotate Page {{ page.page_number }}</h1>

    <div class="relative border inline-block" ref="container">
      <img :src="`/storage/${page.image_path}`" ref="img" class="block" @mousedown="startDrag" />

      <div
        v-for="(box, i) in boxes"
        :key="i"
        :style="boxStyle(box)"
        class="absolute border-2 border-red-500 pointer-events-none"
      ></div>
    </div>

    <div class="mt-4 space-x-2">
      <select v-model="type" class="border p-2">
        <option value="headline">Headline</option>
        <option value="full">Full Article</option>
      </select>
      <button @click="saveBox" class="bg-green-600 text-white px-4 py-2 rounded">Save Region</button>
    </div>

    <div class="mt-4">
      <h2 class="font-bold">Saved Regions</h2>
      <ul>
        <li v-for="(a, i) in page.articles" :key="i">{{ a.type }} - ID: {{ a.id }}</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({ page: Object })

const boxes = reactive([])
const type = ref('headline')

let startX, startY, dragging = false

const startDrag = e => {
  const rect = e.target.getBoundingClientRect()
  startX = e.clientX - rect.left
  startY = e.clientY - rect.top
  dragging = true

  const move = ev => {
    if (!dragging) return
    const currentX = ev.clientX - rect.left
    const currentY = ev.clientY - rect.top
    boxes[0] = {
      x: Math.min(startX, currentX),
      y: Math.min(startY, currentY),
      w: Math.abs(currentX - startX),
      h: Math.abs(currentY - startY),
    }
  }

  const end = ev => {
    window.removeEventListener('mousemove', move)
    window.removeEventListener('mouseup', end)
    dragging = false
  }

  window.addEventListener('mousemove', move)
  window.addEventListener('mouseup', end)
}

const boxStyle = box => ({
  left: box.x + 'px',
  top: box.y + 'px',
  width: box.w + 'px',
  height: box.h + 'px',
})

const saveBox = () => {
  if (boxes.length === 0) return alert('Select a region first.')

  router.post(route('epaper.articles.store'), {
    page_id: props.page.id,
    type: type.value,
    coords: boxes[0],
  }).then(() => {
    boxes.length = 0
  })
}
</script>

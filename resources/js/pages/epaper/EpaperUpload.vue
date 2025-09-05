<script setup>
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  published_at: '',
  pdf: null,
})

const submit = () => {
  form.post(route('epapers.store'))
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-xl font-bold mb-4">Upload New Epaper</h1>
    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-4">
      <input v-model="form.title" type="text" placeholder="Paper Title" class="border p-2 w-full" />
      <input v-model="form.published_at" type="date" class="border p-2 w-full" />
      <input @change="e => form.pdf = e.target.files[0]" type="file" accept="application/pdf" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
    </form>
  </div>
</template>

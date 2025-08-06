<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'

const props = defineProps({
  articles: Array,
})

const approve = (id) => {
  Swal.fire({
    title: 'Setujui artikel ini?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Setujui',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.articles.approve', id))
    }
  })
}

const reject = (id) => {
  Swal.fire({
    title: 'Tolak artikel ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Tolak',
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(route('admin.articles.reject', id))
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Review Artikel" />

    <div class="max-w-5xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-6">Artikel Menunggu Persetujuan</h1>

      <div v-if="articles.length === 0" class="text-gray-500">
        Tidak ada artikel pending.
      </div>

      <div v-else class="space-y-6">
        <div
          v-for="article in articles"
          :key="article.id"
          class="border rounded p-4 shadow-sm bg-white"
        >
          <h2 class="text-xl font-semibold">{{ article.title }}</h2>
          <p class="text-gray-600 mb-2">oleh {{ article.user.name }}</p>
          <p class="mb-4">{{ article.body }}</p>

          <div class="flex gap-4">
            <button
              class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
              @click="approve(article.id)"
            >
              Setujui
            </button>
            <button
              class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
              @click="reject(article.id)"
            >
              Tolak
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

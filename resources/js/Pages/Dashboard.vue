<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  articles: Array
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="space-y-4">
    <h2 class="text-xl font-semibold">Artikel Terbaru</h2>
    <div class="grid gap-4 md:grid-cols-2">
      <div
        v-for="article in articles"
        :key="article.id"
        class="p-4 bg-white rounded-xl shadow"
      >
        <h3 class="text-lg font-bold">{{ article.title }}</h3>
        <p class="text-sm text-gray-500">
          Ditulis oleh: {{ article.user?.name ?? 'Anonim' }}
        </p>
        <p class="mt-2 line-clamp-3 text-gray-700" v-html="article.body">
        </p>
        <a
          :href="route('articles.show', article.id)"
          class="mt-3 inline-block text-indigo-600 hover:underline text-sm"
        >
          Baca Selengkapnya →
        </a>
      </div>
    </div>
  </div>
        </template>
    </AuthenticatedLayout>
</template>

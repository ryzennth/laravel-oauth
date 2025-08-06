<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const form = useForm({
  title: '',
  body: '',
})

const submit = () => {
  form.post(route('articles.store'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Artikel berhasil dikirim dan menunggu persetujuan admin.',
      })
      form.reset()
    },
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tulis Artikel" />

    <div class="max-w-3xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-6">Tulis Artikel</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block mb-1 font-semibold">Judul</label>
          <input v-model="form.title" type="text" class="w-full border rounded p-2" />
          <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
        </div>

        <div>
          <label class="block mb-1 font-semibold">Isi</label>
          <textarea v-model="form.body" rows="8" class="w-full border rounded p-2"></textarea>
          <div v-if="form.errors.body" class="text-red-500 text-sm">{{ form.errors.body }}</div>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Kirim Artikel
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

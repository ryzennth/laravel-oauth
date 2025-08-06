<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const form = useForm({
  name: '',
})

const submit = () => {
  form.post(route('admin.permissions.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tambah Permission" />
    <div class="max-w-xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-4">Tambah Permission</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block font-semibold">Nama</label>
          <input v-model="form.name" type="text" class="w-full border rounded p-2" />
          <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

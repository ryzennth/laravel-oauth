<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({ permission: Object })

const form = useForm({
  name: props.permission.name,
})

const submit = () => {
  form.put(route('admin.permissions.update', props.permission.id))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit Permission" />
    <div class="max-w-xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-4">Edit Permission</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block font-semibold">Nama</label>
          <input v-model="form.name" type="text" class="w-full border rounded p-2" />
          <div v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</div>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

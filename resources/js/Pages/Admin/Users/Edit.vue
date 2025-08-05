<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const { user, roles } = defineProps({
  user: Object,
  roles: Array,
})

const form = useForm({
  name: user.name,
  email: user.email,
  username: user.username,
  role: user.roles[0]?.id || '',
  is_active: user.is_active,
  password: '', // untuk optional update password
})

function submit() {
    console.log(form)
  form.put(route('admin.users.update', user.id), {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Berhasil', 'User diperbarui', 'success')
    },
    onError: () => {
      Swal.fire('Gagal', 'Cek kembali form kamu', 'error')
    },
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit User" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ user.name }}</h2>
    </template>

    <div class="py-6">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow rounded">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <label class="block font-medium text-sm text-gray-700">Name</label>
              <input v-model="form.name" type="text" class="mt-1 block w-full rounded border-gray-300" />
              <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">Username</label>
              <input v-model="form.username" type="text" class="mt-1 block w-full rounded border-gray-300" />
              <div v-if="form.errors.username" class="text-red-600 text-sm mt-1">{{ form.errors.username }}</div>
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">Email</label>
              <input v-model="form.email" type="email" class="mt-1 block w-full rounded border-gray-300" />
              <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">Role</label>
              <select v-model="form.role" class="mt-1 block w-full rounded border-gray-300">
                <option disabled value="">Pilih Role</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                  {{ role.name }}
                </option>
              </select>
              <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">{{ form.errors.role }}</div>
            </div>

            <div>
              <label class="block font-medium text-sm text-gray-700">Password (opsional)</label>
              <input v-model="form.password" type="password" class="mt-1 block w-full rounded border-gray-300" />
              <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</div>
            </div>

            <div>
              <label class="flex items-center space-x-2">
                <input
                type="checkbox"
                :true-value="1"
                :false-value="0"
                v-model="form.is_active"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                />
                <span class="text-sm text-gray-600">Aktifkan akun ini</span>
              </label>
            </div>

            <div class="pt-4">
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

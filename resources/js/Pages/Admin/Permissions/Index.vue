<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

defineProps({ permissions: Array })

const destroyPermission = (id) => {
  Swal.fire({
    title: 'Yakin mau hapus permission ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.permissions.destroy', id))
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Manajemen Permissions" />

    <div class="max-w-4xl mx-auto mt-10">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Permissions</h1>
        <Link :href="route('admin.permissions.create')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Tambah Permission
        </Link>
      </div>

      <table class="w-full table-auto border">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2">Nama</th>
            <th class="px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="permission in permissions" :key="permission.id" class="border-t">
            <td class="px-4 py-2">{{ permission.name }}</td>
            <td class="px-4 py-2 flex gap-2">
              <Link :href="route('admin.permissions.edit', permission.id)" class="text-blue-600 hover:underline">Edit</Link>
              <button @click="destroyPermission(permission.id)" class="text-red-600 hover:underline">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

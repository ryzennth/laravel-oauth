<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  users: Array
});

function deleteUser(id) {
  Swal.fire({
    title: 'Yakin ingin hapus user ini?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.users.destroy', id));
    }
  });
}
</script>

<template>
  <Head title="Manajemen User" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen User</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
          <Link
            href="/admin/users/create"
            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
          >
            + Tambah
          </Link>
        </div>

        <div class="overflow-x-auto bg-white shadow sm:rounded-lg">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Nama
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Username
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Email
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                  Role
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="user in users" :key="user.id">
                <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ user.username }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                <td class="text-gray-800 text-sm">
                    <span v-if="user.roles.length">{{ user.roles[0].name }}</span>
                    <span v-else>-</span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <Link
                    :href="route('admin.users.edit', user.id)"
                    class="mr-2 text-indigo-600 hover:text-indigo-900"
                  >
                    Edit
                  </Link>
                  <button
                    @click="deleteUser(user.id)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Hapus
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
th, td {
  white-space: nowrap;
}
</style>

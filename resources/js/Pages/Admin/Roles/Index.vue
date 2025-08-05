<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  roles: Array
})
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Role</h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded p-6">
          <table class="w-full table-auto border">
            <thead>
              <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left border-b">Nama Role</th>
                <th class="px-4 py-2 text-left border-b">Permissions</th>
                <th class="px-4 py-2 text-left border-b">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b font-semibold">{{ role.name }}</td>
                <td class="px-4 py-2 border-b text-sm text-gray-600">
                  <span
                    v-if="role.permissions.length"
                    v-for="permission in role.permissions"
                    :key="permission.id"
                    class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-1 mb-1"
                  >
                    {{ permission.name }}
                  </span>
                  <span v-else class="text-gray-400 italic">Belum ada</span>
                </td>
                <td class="px-4 py-2 border-b">
                  <Link
                    :href="route('admin.roles.edit', role.id)"
                    class="text-sm text-indigo-600 hover:underline"
                  >
                    Edit Permissions
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

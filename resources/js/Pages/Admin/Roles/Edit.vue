<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ role: Object, permissions: Array });

const form = useForm({
  permissions: props.role.permissions.map(p => p.id),
});

function submit() {
  form.put(route('admin.roles.update', role.id));
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Permissions: {{ props.role.name }}
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
          <form @submit.prevent="submit">
            <h2 class="text-lg font-semibold mb-4">Pilih Permissions</h2>

            <div v-for="permission in permissions" :key="permission.id" class="mb-2">
              <label class="inline-flex items-center">
                <input
                  type="checkbox"
                  :value="permission.id"
                  v-model="form.permissions"
                  class="form-checkbox"
                />
                <span class="ml-2">{{ permission.name }}</span>
              </label>
            </div>

            <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
              Simpan Perubahan
            </button>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

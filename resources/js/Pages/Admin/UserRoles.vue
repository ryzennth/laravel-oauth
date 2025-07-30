<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

defineProps({
    users: Array,
    roles: Array
})

const forms = ref({})

const setupForms = (users, roles) => {
    users.forEach(user => {
        forms.value[user.id] = useForm({
            role: user.role || roles[0]
        })
    })
}

setupForms(__props.users, __props.roles)

const assignRole = async (userId) => {
    const form = forms.value[userId]

    const confirm = await Swal.fire({
        title: 'Ubah Role?',
        text: "Perubahan akan langsung berlaku.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan!',
    })

    if (confirm.isConfirmed) {
        form.post(route('user.roles.assign', userId), {
            preserveScroll: true,
            onSuccess: () => Swal.fire('Berhasil!', 'Role diperbarui.', 'success'),
            onError: () => Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error'),
        })
    }
}
</script>

<template>
    <Head title="Kelola Role User" />
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">Kelola Role User</h1>

        <div v-for="user in users" :key="user.id" class="border rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="forms[user.id].role" class="rounded border-gray-300">
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <button @click="assignRole(user.id)" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

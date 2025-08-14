<script setup>
import { Head, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    articles: Object
})

const approve = (id) => {
    Swal.fire({
        title: 'Setujui Artikel?',
        text: 'Artikel akan disetujui dan siap dipublikasikan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('admin.articles.approve', id))
        }
    })
}

const reject = (id) => {
    Swal.fire({
        title: 'Tolak Artikel',
        input: 'textarea',
        inputLabel: 'Alasan Penolakan',
        inputPlaceholder: 'Tuliskan alasan kenapa artikel ditolak...',
        showCancelButton: true,
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) return 'Alasan wajib diisi!'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('admin.articles.reject', id), { reason: result.value })
        }
    })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Review Artikel" />
    <div class="p-6">
        <h1 class="text-xl font-bold mb-4">Review Artikel</h1>

        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Judul</th>
                    <th class="border p-2">Penulis</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in articles.data" :key="a.id">
                    <td class="border p-2">{{ a.title }}</td>
                    <td class="border p-2">{{ a.user.name }}</td>
                    <td class="border p-2 capitalize">{{ a.status }}</td>
                    <td class="border p-2 space-x-2">
                        <button @click="approve(a.id)" class="text-green-600">Approve</button>
                        <button @click="reject(a.id)" class="text-red-600">Reject</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
  </AuthenticatedLayout>
</template>

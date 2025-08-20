<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    articles: Object
})

const resubmit = (id) => {
    Swal.fire({
        title: 'Ajukan Ulang?',
        text: 'Artikel akan diajukan kembali untuk ditinjau admin.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ajukan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('author.articles.resubmit', id))
        }
    })
}

const deleteArticle = (id) => {
    Swal.fire({
        title: 'Hapus Artikel?',
        text: 'Artikel akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('author.articles.destroy', id))
        }
    })
}
</script>

<template>
    <AuthenticatedLayout>
    <Head title="Artikel Saya" />
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold">Artikel Saya</h1>
            <Link href="/articles/create" class="px-4 py-2 bg-blue-600 text-white rounded">Buat Artikel</Link>
        </div>

        <table class="w-full border-collapse border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">Judul</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Terakhir Diperbarui</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in articles.data" :key="a.id">
                    <td class="border p-2">{{ a.title }}</td>
                    <td class="border p-2 capitalize">
                        {{ a.status }}
                        <div v-if="a.status === 'rejected'" class="text-red-600 text-sm">
                            Alasan: {{ a.reject_reason }}
                        </div>
                    </td>
                    <td class="border p-2">{{ new Date(a.updated_at).toLocaleString() }}</td>
                    <td class="border p-2 space-x-2">
                        <Link :href="route('articles.edit', a.id)" class="text-blue-600">Edit</Link>
                        <button @click="deleteArticle(a.id)" class="text-red-600">Hapus</button>
                        <button v-if="a.status === 'rejected'" @click="resubmit(a.id)" class="text-green-600">
                            Ajukan Ulang
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</AuthenticatedLayout>
</template>

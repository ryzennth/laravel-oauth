<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    googleUser: Object,
});

const loading = ref(false);

onMounted(() => {
    if (!props.googleUser) {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Data akun Google tidak ditemukan.',
        }).then(() => {
            router.visit('/login');
        });
    }
});

const handleConfirm = () => {
    loading.value = true;
    router.post('/google/confirm', {}, {
        onFinish: () => loading.value = false,
    });
};
</script>

<template>
    <Head title="Verifikasi Google" />

    <GuestLayout>
        <div class="w-full max-w-md mx-auto p-6 bg-white shadow rounded-xl">
            <h1 class="text-2xl font-bold text-center mb-4">Verifikasi Akun Google</h1>

            <div class="flex items-center justify-center mb-4">
                <img
                    v-if="googleUser?.avatar"
                    :src="googleUser.avatar"
                    alt="Avatar"
                    class="w-20 h-20 rounded-full shadow"
                />
            </div>

            <div class="text-center mb-6">
                <p class="text-lg font-semibold">{{ googleUser?.name }}</p>
                <p class="text-sm text-gray-500">{{ googleUser?.email }}</p>
            </div>

            <button
                @click="handleConfirm"
                :disabled="loading"
                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"
            >
                {{ loading ? 'Memproses...' : 'Lanjutkan' }}
            </button>
        </div>
    </GuestLayout>
</template>

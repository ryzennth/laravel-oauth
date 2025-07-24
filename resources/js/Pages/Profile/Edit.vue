<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import Swal from 'sweetalert2';

// Props dari backend
defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    auth: Object,
});

// Ambil props flash dari page
const page = usePage();

// Jalankan popup kalau harus isi password (jika flash sudah tersedia saat mounted)
onMounted(() => {
    const flash = page.props.flash;

    if (flash?.mustSetPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Lengkapi Password',
            text: 'Silakan isi password terlebih dahulu sebelum melanjutkan.',
            confirmButtonText: 'Isi Sekarang',
        });
    } else if (flash?.mustCompleteProfile) {
        Swal.fire({
            icon: 'warning',
            title: 'Lengkapi Profil',
            text: 'Silakan isi username terlebih dahulu sebelum melanjutkan.',
            confirmButtonText: 'Isi Sekarang',
        });
    }
});


// Tambahan: watch agar tetap muncul meskipun flash datang belakangan
watch(
    () => page.props.flash?.mustSetPassword,
    (newVal) => {
        if (newVal) {
            Swal.fire({
                icon: 'warning',
                title: 'Lengkapi Password',
                text: 'Silakan isi password terlebih dahulu sebelum melanjutkan.',
                confirmButtonText: 'Isi Sekarang',
            });
        }
    },
    { immediate: true }
);


</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informasi Profil -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        :user="auth.user"
                    />
                </div>

                <!-- Update Password -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Hapus Akun -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

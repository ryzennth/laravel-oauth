<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email" />

        <div class="mb-4 text-sm text-gray-600">
            Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda
            dengan mengklik tautan yang telah kami kirimkan. Jika Anda belum menerima email tersebut,
            kami akan dengan senang hati mengirim ulang. <br>
            Catatan: jika anda login lewat google, silahkan saat diarahkan ke halaman login kembali login menggunakan google
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 text-sm font-medium text-green-600"
        >
            Link verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat mendaftar.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Kirim Ulang Email Verifikasi
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Keluar
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>

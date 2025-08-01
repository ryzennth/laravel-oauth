<script setup>
import { ref, watch } from 'vue'; // <== tambahkan watch
import { useForm, Link } from '@inertiajs/vue3';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';


const props = defineProps({
    user: {
        type: Object,
        required: true,
        default: () => ({
            name: '',
            username: '',
            email: '',
            profile_photo_path: null,
            email_verified_at: null,
        }),
    },
    mustVerifyEmail: Boolean,
    status: String,
});

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    profile_photo: null,
});

const usernameError = ref('');

// ✅ Gunakan arrow function agar username bisa di-watch
watch(() => form.username, (val) => {
    if (val && !/^[a-z0-9]+$/.test(val)) {
        usernameError.value = 'Username hanya boleh huruf kecil dan angka tanpa spasi atau simbol.';
    } else {
        usernameError.value = '';
    }
});

const photoPreview = ref(null);

// ✅ Jangan kirim form jika ada error
const updateProfileInformation = () => {
    if (usernameError.value) return;

    form.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const selectNewPhoto = (event) => {
    form.profile_photo = event.target.files[0];
    if (form.profile_photo) {
        photoPreview.value = URL.createObjectURL(form.profile_photo);
    }
};

const updateProfilePhoto = () => {
    form.post(route('profile.photo.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('profile_photo');
            photoPreview.value = null;
        },
    });
};
</script>


<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Perbarui informasi profil dan alamat email akunmu.
            </p>
        </header>

        <!-- Formulir Informasi Profil -->
        <form @submit.prevent="updateProfileInformation" class="mt-6 space-y-6">
            <!-- Nama -->
            <div>
                <InputLabel for="name" value="Nama" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    :placeholder="props.user.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Username -->
            <div>
                <InputLabel for="username" value="Username" />
                <TextInput
                    id="username"
                    v-model="form.username"
                    type="text"
                    class="mt-1 block w-full"
                    :placeholder="props.user.username"
                    required
                    autocomplete="username"
                />
                <p v-if="usernameError" class="text-red-500 text-sm mt-1">{{ usernameError }}</p>
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    :placeholder="props.user.email"
                    required
                    autocomplete="email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Verifikasi Email -->
            <div v-if="props.mustVerifyEmail && props.user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>
                <div
                    v-show="props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>

                <p
                    v-if="props.status === 'profile-updated'"
                    class="text-sm font-medium text-green-600"
                >
                    Profil berhasil diperbarui.
                </p>
            </div>
        </form>

        <!-- Foto Profil -->
        <div class="mt-6">
            <h2 class="text-lg font-medium text-gray-900">Foto Profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Unggah atau perbarui foto profil Anda.
            </p>

            <div class="mt-4">
                <img
                    v-if="props.user.profile_photo_path || photoPreview"
                    :src="photoPreview || '/storage/' + props.user.profile_photo_path"
                    alt="Foto Profil"
                    class="w-32 h-32 rounded-full object-cover"
                />
                <p v-else class="text-gray-500">Belum ada foto profil.</p>
            </div>

            <form @submit.prevent="updateProfilePhoto" class="mt-4 space-y-4">
                <div>
                    <InputLabel for="profile_photo" value="Pilih Foto" />
                    <input
                        id="profile_photo"
                        type="file"
                        accept="image/*"
                        class="mt-1 block w-full"
                        @change="selectNewPhoto"
                    />
                    <InputError class="mt-2" :message="form.errors.profile_photo" />
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing || !form.profile_photo">
                        Unggah
                    </PrimaryButton>

                    <p
                        v-if="props.status === 'profile-photo-updated'"
                        class="text-sm font-medium text-green-600"
                    >
                        Foto profil berhasil diperbarui.
                    </p>
                </div>
            </form>
        </div>
    </section>
</template>

<script setup>
import { ref, watch } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const usernameWarning = ref('');

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Realtime warning jika username mengandung spasi
watch(() => form.username, (val) => {
    usernameWarning.value = /\s/.test(val)
        ? 'Username tidak boleh mengandung spasi.'
        : '';
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <!-- Name -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Username -->
            <div class="mt-4">
                <InputLabel for="username" value="Username" />
                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                    autocomplete="username"
                />
                <!-- Warning jika ada spasi -->
                <p v-if="usernameWarning" class="text-sm text-red-600 mt-1">
                    {{ usernameWarning }}
                </p>
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 focus:outline-none"
                        @click="showPassword = !showPassword"
                    >
                        <component
                            :is="showPassword ? EyeSlashIcon : EyeIcon"
                            class="h-5 w-5"
                        />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <div class="relative">
                    <TextInput
                        id="password_confirmation"
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-500 focus:outline-none"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                    >
                        <component
                            :is="showPasswordConfirm ? EyeSlashIcon : EyeIcon"
                            class="h-5 w-5"
                        />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Footer -->
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-sm text-gray-600">Sudah punya akun?</span>
                    <Link
                        :href="route('login')"
                        class="ml-1 text-sm text-blue-600 hover:underline"
                    >
                        Login
                    </Link>
                </div>

                <PrimaryButton
                    class="w-full sm:w-auto"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

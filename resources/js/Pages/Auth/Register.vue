<script setup>
import { ref, watch } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';


const showPassword = ref(false);
const showPasswordConfirm = ref(false);
const usernameWarning = ref('');

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    'g-recaptcha-response': '',
});

// Realtime warning jika username mengandung spasi
const usernameError = ref('');

// ✅ Gunakan arrow function agar username bisa di-watch
watch(() => form.username, (val) => {
    if (val && !/^[a-z0-9]+$/.test(val)) {
        usernameError.value = 'Username hanya boleh huruf kecil dan angka tanpa spasi atau simbol.';
    } else {
        usernameError.value = '';
    }
});
const passwordError = ref('');

watch(() => form.password, (val) => {
    const rules = [
        { check: val.length >= 8, message: 'minimal 8 karakter' },
        { check: /[a-z]/.test(val), message: 'huruf kecil' },
        { check: /[A-Z]/.test(val), message: 'huruf besar' },
        { check: /[0-9]/.test(val), message: 'angka' },
        { check: /[!@#$%^&*(),.?":{}|<>_\-\\[\]=+~`/]/.test(val), message: 'simbol' }, // simbol ditambahkan
    ];

    const failed = rules.filter(rule => !rule.check);
    if (val && failed.length > 0) {
        passwordError.value = 'Password harus mengandung: ' + failed.map(r => r.message).join(', ');
    } else {
        passwordError.value = '';
    }
});

const submit = () => {
    const response = grecaptcha.getResponse();
    if (!response) {
        alert('Silakan selesaikan CAPTCHA terlebih dahulu.');
        return;
    }

    form['g-recaptcha-response'] = response;

    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            grecaptcha.reset(); // reset box
        },
    });
};


onMounted(() => {
    const script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoadCallback&render=explicit';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    // Buat callback global (biar dikenali oleh reCAPTCHA)
    window.onRecaptchaLoadCallback = () => {
        grecaptcha.render('recaptcha-box', {
            sitekey: '6Lec7pIrAAAAAHcKgSfKTXdwYnUlgLlRZ2O7zN_u',
        });
    };
});








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
                <p v-if="usernameError" class="text-red-500 text-sm mt-1">{{ usernameError }}</p>
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
                <p v-if="passwordError" class="text-red-500 text-sm mt-1">{{ passwordError }}</p>
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
            <div id="recaptcha-box" class="g-recaptcha flex justify-center" data-sitekey="6Lec7pIrAAAAAHcKgSfKTXdwYnUlgLlRZ2O7zN_u"></div>
            <InputError class="mt-2" :message="form.errors['g-recaptcha-response']" />
        </form>



    </GuestLayout>
</template>

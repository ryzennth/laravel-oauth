<script setup>
import { ref, watch} from 'vue' 
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid' 

import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm} from '@inertiajs/vue3';
import { onMounted } from 'vue';

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
    'g-recaptcha-response': '',
});

const submit = () => {
    const response = grecaptcha.getResponse();
    if (!response) {
        alert('Silakan selesaikan CAPTCHA terlebih dahulu.');
        return;
    }

    form['g-recaptcha-response'] = response;

    form.post(route('password.store'), {
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

</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

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

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </PrimaryButton>
            </div>
            <div id="recaptcha-box" class="g-recaptcha flex justify-center" data-sitekey="6Lec7pIrAAAAAHcKgSfKTXdwYnUlgLlRZ2O7zN_u"></div>
            <InputError class="mt-2" :message="form.errors['g-recaptcha-response']" />
        </form>
    </GuestLayout>
</template>

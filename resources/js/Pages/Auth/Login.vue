<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'

// Heroicons
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

const page = usePage()
const status = page.props?.flash?.status ?? null


const form = useForm({
    login: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
}

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

const loginWithGoogle = () => {
    window.location.href = route('auth.google.redirect')
}
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Flash Message -->
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Login -->
            <div>
                <InputLabel for="login" value="Email / Username" />
                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <!-- Password with toggle -->
            <div>
                <InputLabel for="password" value="Password" />
                <div class="relative">
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        v-model="form.password"
                        required
                    />
                    <button
                        type="button"
                        @click="togglePasswordVisibility"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500"
                        tabindex="-1"
                    >
                        <component
                            :is="showPassword ? EyeSlashIcon : EyeIcon"
                            class="h-5 w-5"
                        />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="ms-2 text-sm text-gray-600">Remember me</span>
            </div>

            <!-- Login Button -->
            <div class="flex justify-center">
                <button
                    type="submit"
                    class="w-full sm:w-72 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm transition"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </button>
            </div>
        </form>

        <!-- atau -->
        <div class="my-4 text-center text-sm text-gray-500">atau</div>

        <!-- Google Login Button -->
        <div class="flex justify-center">
            <button
                type="button"
                @click="loginWithGoogle"
                class="w-full sm:w-72 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition"
            >
                Login dengan Google
            </button>
        </div>

        <!-- Register Link -->
        <div class="mt-4 text-center">
            <span class="text-sm text-gray-600">Belum punya akun?</span>
            <Link
                :href="route('register')"
                class="ml-1 text-sm text-blue-600 hover:underline"
            >
                Daftar
            </Link>
        </div>
    </GuestLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.set.store'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Set Password" />

        <h1 class="text-xl font-bold mb-4">Buat Password</h1>

        <form @submit.prevent="submit" class="space-y-4 max-w-md">
            <div>
                <InputLabel for="password" value="Password Baru" />
                <TextInput id="password" type="password" v-model="form.password" class="mt-1 block w-full" required />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full" required />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <PrimaryButton :disabled="form.processing" class="mt-4">Simpan Password</PrimaryButton>
        </form>
    </GuestLayout>
</template>

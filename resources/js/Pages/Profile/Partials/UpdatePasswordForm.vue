<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import Swal from 'sweetalert2';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid';

const page = usePage();
const mustSetPassword = computed(() => page.props.mustSetPassword === true);

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);
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


// Swal muncul otomatis saat halaman dimuat
onMounted(() => {
    if (mustSetPassword.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Password Belum Diatur',
            text: 'Silakan buat password baru untuk mengamankan akunmu.',
            confirmButtonText: 'Isi Sekarang',
        }).then(() => {
            passwordInput.value?.focus();
        });
    }
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: form.errors.password,
                });
            }

            if (!mustSetPassword.value && form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();

                Swal.fire({
                    icon: 'error',
                    title: 'Password Saat Ini Salah',
                    text: form.errors.current_password,
                });
            }
        },
    });
};
</script>


<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600">
                Pertimbangkan akunmu untuk tetap aman dengan memperbarui kata sandi secara berkala. Pastikan kata sandi baru yang kamu buat kuat dan unik.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <!-- Notice for Google/OAuth users -->
            <div v-if="mustSetPassword" class="text-sm text-yellow-600 bg-yellow-100 border border-yellow-300 p-3 rounded">
                Kamu belum memiliki password. Silakan isi password baru untuk mengamankan akunmu.
            </div>

            <!-- Current Password -->
            <div v-if="!mustSetPassword">
                <InputLabel for="current_password" value="Current Password" />

                <div class="relative">
                    <TextInput
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showCurrentPassword = !showCurrentPassword"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500"
                        tabindex="-1"
                    >
                        <component :is="showCurrentPassword ? EyeSlashIcon : EyeIcon" class="h-5 w-5" />
                    </button>
                </div>
                
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <!-- New Password -->
            <div>
                <InputLabel for="password" value="New Password" />

                <div class="relative">
                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        :type="showNewPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        @click="showNewPassword = !showNewPassword"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500"
                        tabindex="-1"
                    >
                        <component :is="showNewPassword ? EyeSlashIcon : EyeIcon" class="h-5 w-5" />
                    </button>
                </div>
                <p v-if="passwordError" class="text-red-500 text-sm mt-1">{{ passwordError }}</p>

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <div class="relative">
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        class="mt-1 block w-full pr-10"
                        autocomplete="new-password"
                    />
                    <button
                        type="button"
                        @click="showConfirmPassword = !showConfirmPassword"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500"
                        tabindex="-1"
                    >
                        <component :is="showConfirmPassword ? EyeSlashIcon : EyeIcon" class="h-5 w-5" />
                    </button>
                </div>

                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <p class="text-sm text-gray-500 mt-1">
                Password minimal 8 karakter, dan harus mengandung huruf besar, huruf kecil, angka, dan simbol.
            </p>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

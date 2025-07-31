<script setup>
import { computed, ref, watch } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'

const form = useForm({
  name: '',
  username: '',
  email: '',
  password: '',
  role: 'user',
})

// Error refs untuk validasi real-time
const usernameError = ref('')
const passwordError = ref('')

// Validasi real-time username
watch(() => form.username, (val) => {
  if (val && !/^[a-z0-9]+$/.test(val)) {
    usernameError.value = 'Username hanya boleh huruf kecil dan angka tanpa spasi atau simbol.'
  } else {
    usernameError.value = ''
  }
})

// Validasi real-time password
watch(() => form.password, (val) => {
  const rules = [
    { check: val.length >= 8, message: 'minimal 8 karakter' },
    { check: /[a-z]/.test(val), message: 'huruf kecil' },
    { check: /[A-Z]/.test(val), message: 'huruf besar' },
    { check: /[0-9]/.test(val), message: 'angka' },
    { check: /[!@#$%^&*(),.?":{}|<>_\-\\[\]=+~`/]/.test(val), message: 'simbol' },
  ]
  const failed = rules.filter(rule => !rule.check)
  if (val && failed.length > 0) {
    passwordError.value = 'Password harus mengandung: ' + failed.map(r => r.message).join(', ')
  } else {
    passwordError.value = ''
  }
})

// Validasi lokal (real-time)
const validationErrors = computed(() => {
  const errors = {}

  if (form.name.length < 3) {
    errors.name = 'Nama minimal 3 karakter.'
  }

  if (form.username.length < 3) {
    errors.username = 'Username minimal 3 karakter.'
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.email)) {
    errors.email = 'Email tidak valid.'
  }

  if (form.password.length < 8) {
    errors.password = 'Password minimal 8 karakter.'
  }

  if (!['user', 'admin', 'super_admin'].includes(form.role)) {
    errors.role = 'Role tidak valid.'
  }

  return errors
})

// Gak bisa submit kalau masih ada error lokal atau error real-time
const canSubmit = computed(() =>
  Object.keys(validationErrors.value).length === 0 &&
  !usernameError.value &&
  !passwordError.value
)

const submit = () => {
  if (!canSubmit.value) return
  form.post(route('admin.users.store'))
}
</script>

<template>
  <Head title="Tambah User" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah User</h2>
    </template>

    <div class="max-w-3xl mx-auto py-12 px-6 bg-white rounded-xl shadow-md">
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <InputLabel for="name" value="Nama" />
          <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
          <InputError :message="form.errors.name || validationErrors.name" class="mt-2" />
        </div>

        <div>
          <InputLabel for="username" value="Username" />
          <TextInput id="username" v-model="form.username" type="text" class="mt-1 block w-full" required />
          <InputError :message="form.errors.username || validationErrors.username || usernameError" class="mt-2" />
        </div>

        <div>
          <InputLabel for="email" value="Email" />
          <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
          <InputError :message="form.errors.email || validationErrors.email" class="mt-2" />
        </div>

        <div>
          <InputLabel for="password" value="Password" />
          <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required />
          <InputError :message="form.errors.password || validationErrors.password || passwordError" class="mt-2" />
        </div>

        <div>
          <InputLabel for="role" value="Role" />
          <select id="role" v-model="form.role" class="mt-1 block w-full rounded border-gray-300">
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
          <InputError :message="form.errors.role || validationErrors.role" class="mt-2" />
        </div>

        <div class="text-right mt-8">
          <PrimaryButton :disabled="form.processing || !canSubmit" class="opacity-90 disabled:opacity-50">
            Tambah User
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
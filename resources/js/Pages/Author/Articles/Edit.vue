<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { onMounted, ref } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

const props = defineProps({
  article: Object,
})

const form = useForm({
  title: props.article.title,
  body: props.article.body,
})

const editor = ref(null)
let quillInstance = null

// Inisialisasi Quill saat mount
onMounted(() => {
  quillInstance = new Quill(editor.value, {
    theme: 'snow',
    placeholder: 'Tulis konten artikelnya di sini...',
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'image'],
        ['clean']
      ]
    }
  })

  // Set isi editor dari article.body
  quillInstance.root.innerHTML = props.article.body || ''

  // Update form.body setiap ada perubahan
  quillInstance.on('text-change', () => {
    form.body = quillInstance.root.innerHTML
  })

  // Kalau artikel sudah published → disable editor
  if (props.article.status === 'published') {
    quillInstance.disable()
  }
})

// submit edit
function submit() {
  form.put(route('author.articles.update', props.article.id))
}
</script>

<template>
  <Head title="Edit Artikel" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Artikel
      </h2>
    </template>

    <div class="py-8">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit" class="space-y-6">

            <!-- Title -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Judul Artikel
              </label>
              <input
                type="text"
                v-model="form.title"
                class="w-full rounded-lg border-gray-300"
                :disabled="props.article.status === 'published'"
              />
              <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
            </div>

            <!-- Body pakai Quill -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Konten
              </label>
              <div ref="editor" class="bg-white text-gray-800 min-h-[300px] rounded-lg"></div>
              <div v-if="form.errors.body" class="text-red-500 text-sm mt-1">{{ form.errors.body }}</div>
            </div>

            <!-- Status Info -->
            <div v-if="props.article.status === 'rejected'" class="bg-red-100 text-red-800 p-3 rounded-lg">
              <p class="font-semibold">Artikel ini ditolak</p>
              <p v-if="props.article.rejection_reason" class="text-sm">{{ props.article.rejection_reason }}</p>
            </div>
            <div v-else-if="props.article.status === 'pending'" class="bg-yellow-100 text-yellow-800 p-3 rounded-lg">
              Artikel ini sedang menunggu persetujuan admin.
            </div>
            <div v-else-if="props.article.status === 'published'" class="bg-green-100 text-green-800 p-3 rounded-lg">
              Artikel ini sudah dipublikasikan dan tidak dapat diedit.
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4">
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 disabled:opacity-50"
                :disabled="props.article.status === 'published' || form.processing"
              >
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

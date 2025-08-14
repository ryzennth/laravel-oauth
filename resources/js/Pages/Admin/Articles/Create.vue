<script setup>
import { ref, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'


const form = useForm({
  title: '',
  body: '',
})

const editorContent = ref('')

// Sinkronkan Quill editor ke form.body
watch(editorContent, (val) => {
  form.body = val
})

// Handler submit
const submit = () => {
  form.post(route('articles.store'), {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Artikel berhasil dikirim dan menunggu persetujuan admin.',
      })
      form.reset()
      editorContent.value = ''
    },
  })
}

// Custom handler untuk upload gambar di Quill
function imageHandler() {
  const input = document.createElement('input')
  input.setAttribute('type', 'file')
  input.setAttribute('accept', 'image/*')
  input.click()

  input.onchange = async () => {
    const file = input.files[0]
    if (!file) return

    const formData = new FormData()
    formData.append('image', file)

    const res = await fetch(route('upload.image'), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    })

    const data = await res.json()
    if (data.url) {
      const range = quillRef.value.getEditor().getSelection()
      quillRef.value.getEditor().insertEmbed(range.index, 'image', data.url)
    }
  }
}

const quillRef = ref(null)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Tulis Artikel" />

    <div class="max-w-3xl mx-auto mt-10">
      <h1 class="text-2xl font-bold mb-6">Tulis Artikel</h1>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block mb-1 font-semibold">Judul</label>
          <input v-model="form.title" type="text" class="w-full border rounded p-2" />
          <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
        </div>

        <div>
          <label class="block mb-1 font-semibold">Isi</label>
          <QuillEditor
            ref="quillEditor"
            v-model:content="editorContent"
            content-type="html"
            theme="snow"
            toolbar="full"
            :modules="modules"
            style="min-height: 200px"
          />
          <div v-if="form.errors.body" class="text-red-500 text-sm">{{ form.errors.body }}</div>
        </div>

        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
        >
          Kirim Artikel
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

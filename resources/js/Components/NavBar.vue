<template>
  <nav class="bg-blue-600 px-4 py-2 flex items-center justify-between">
    <div class="flex items-center space-x-4">
      <span class="text-white font-bold text-lg">OAuth App</span>
      <a href="/" class="text-white hover:underline">Home</a>
      <a href="/about" class="text-white hover:underline">About</a>
      <a href="/profile" class="text-white hover:underline">Profile</a>
    </div>
    <div>
      <button
        v-if="!isLoggedIn"
        @click="login"
        class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-blue-100"
      >
        Login
      </button>
      <button
        v-else
        @click="logout"
        class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-blue-100"
      >
        Logout
      </button>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue'

const isLoggedIn = ref(false)

function login() {
  // Implementasi login
  isLoggedIn.value = true
}

function logout() {
  // Implementasi logout
  isLoggedIn.value = false
}
</script>
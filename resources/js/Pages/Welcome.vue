<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const { props } = usePage()
const articles = props.articles || []
const user = props.auth?.user

defineProps({
  articles: Array
})

</script>

<template>
  <div class="min-h-screen bg-gray-200 dark:bg-gray-800">
    <!-- Navbar -->
    <nav class="fixed top-0 w-full flex items-center justify-between bg-sky-400 px-6 py-3 shadow z-50 dark:bg-sky-800">
      <div class="text-3xl font-extrabold text-black">FYN</div>

      <div class="flex space-x-8 text-lg">
        <Link href="/" class="hover:underline transition duration-300">Home</Link>
        <Link href="/news" class="hover:underline transition duration-300">News</Link>
        <Link href="/category" class="hover:underline transition duration-300">Category</Link>
      </div>

      <div>
        <template v-if="user">
<div class="hidden sm:ms-6 sm:flex sm:items-center">
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md border border-transparent  px-3 py-2 text-sm font-medium leading-4 text-black hover:text-gray-700 transition focus:outline-none"
                                        >
                                            <div class="flex items-center space-x-2">
                                                <img
                                                    :src="user.profile_photo_path ? `/storage/${user.profile_photo_path}` : '/default-profile.png'"

                                                    alt="Profile Photo"
                                                    class="h-10 w-10 rounded-full object-cover border border-transparent"
                                                />
                                                <div class="text-left">
                                                    <div class="flex items-center gap-2">
                                                        <span>{{ user.name }}</span>
                                                        <span
                                                            v-for="role in roles"
                                                            :key="role"
                                                            class="text-[10px] uppercase bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold"
                                                        >
                                                            {{ role }}
                                                        </span>
                                                    </div>
                                                    <div class="text-xs text-gray-800">{{ user.username }}</div>
                                                </div>
                                            </div>
                                            <svg
                                                class="ml-2 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
        </template>
        <template v-else>
          <div class="flex space-x-3">
            <Link
              href="/login"
              class="px-4 py-2 bg-white text-sky-600 font-semibold rounded hover:bg-gray-100 transition duration-300"
            >
              Login
            </Link>
            <Link
              href="/register"
              class="px-4 py-2 bg-sky-600 text-white font-semibold rounded hover:bg-sky-700 transition duration-300"
            >
              Register
            </Link>
          </div>
        </template>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20 px-6 grid grid-cols-1 md:grid-cols-3 gap-4 p-6">
      <!-- Artikel Utama -->
      <transition-group
        name="fade"
        tag="div"
        class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4"
      >
        <div
          v-for="article in articles"
          :key="article.id"
          class="p-4 bg-white rounded-xl shadow overflow-hidden hover:shadow-lg hover:scale-[1.02] transition transform duration-300"
        >
          <img 
            v-if="article.cover_image" 
            :src="`/storage/${article.cover_image}`" 
            alt="Cover Image" 
            class="w-full h-60 object-cover rounded-lg mb-3"
          />

          <h3 class="text-lg font-bold">{{ article.title }}</h3>
          <p class="text-sm text-gray-500">
            Ditulis oleh: {{ article.user?.name ?? 'Anonim' }}
          </p>
          <p class="mt-2 line-clamp-3 text-gray-700" v-html="article.body"></p>
          <Link
            :href="route('articles.show', article.id)"
            class="mt-3 inline-block text-indigo-600 hover:underline text-sm"
          >
            Baca Selengkapnya →
          </Link>
        </div>
      </transition-group>

      <!-- Sidebar Top Artikel -->
      <aside class="space-y-4">
        <h3 class="text-lg font-bold">Top Artikel</h3>
        <transition-group name="fade" tag="div" class="space-y-4">
          <div
            v-for="top in articles.slice(0, 5)"
            :key="'top-' + top.id"
            class="bg-sky-300 rounded-lg overflow-hidden shadow hover:shadow-lg hover:scale-[1.03] transition transform duration-300"
          >
            <img
              v-if="top.cover_image"
              :src="`/storage/${top.cover_image}`"
              alt="top article"
              class="w-full h-20 object-cover"
            />
            <p class="p-2 text-sm font-semibold truncate">{{ top.title }}</p>
          </div>
        </transition-group>
      </aside>
    </main>
  </div>
</template>

<style>
/* Fade in animation */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.6s ease, transform 0.6s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(15px);
}
</style>

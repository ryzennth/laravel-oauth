<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-gray-50 text-gray-700 dark:bg-gray-900 dark:text-gray-300">
        <!-- Navigation -->
        <header class="w-full max-w-7xl mx-auto px-6 py-4 flex justify-end items-center">
            <nav v-if="canLogin" class="flex gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-4 py-2 rounded-md border border-transparent bg-black text-white hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 transition"
                >
                    Dashboard
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 rounded-md text-black hover:text-gray-900 dark:text-white dark:hover:text-gray-600 transition"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-4 py-2 rounded-md text-black hover:text-gray-900 dark:text-white dark:hover:text-gray-600 transition"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex flex-col items-center justify-center px-6 py-12 text-center">
            <h1 class="text-4xl font-bold mb-4">Welcome!</h1>
            <p class="text-lg mb-8">
                Based on Laravel {{ laravelVersion }} & PHP {{ phpVersion }}
            </p>

            <!-- Screenshot -->
            <div id="screenshot-container" class="w-full max-w-4xl">
                <img
                    src="/images/screenshot.png"
                    alt="Screenshot"
                    @error="handleImageError"
                    class="rounded-lg shadow-lg"
                />
            </div>
        </main>

        <!-- Footer -->
        <footer class="text-center py-6 text-sm text-gray-400 dark:text-gray-600">
            &copy; {{ new Date().getFullYear() }} Fauzan All rights reserved.
        </footer>
    </div>
</template>

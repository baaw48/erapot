<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const isDarkMode = ref(false);

onMounted(() => {
    const saved = localStorage.getItem('darkMode');
    if (saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }
});

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
};
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-100 dark:bg-slate-900 px-4 py-8 transition-colors duration-300">
        <!-- Dark mode toggle -->
        <button
            @click="toggleDarkMode"
            class="absolute top-4 right-4 p-2 rounded-lg bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 transition-all hover:shadow-md"
        >
            <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        <!-- Logo & Title -->
        <div class="mb-6 text-center">
            <Link href="/" class="flex items-center justify-center gap-3">
                <div class="p-3 bg-primary-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </Link>
            <h1 class="mt-4 text-2xl font-bold text-slate-800 dark:text-white">E-RAPOR</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sistem Informasi Raport Digital</p>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-6 transition-colors duration-300">
            <slot />
        </div>

        <!-- Footer -->
        <p class="mt-6 text-xs text-slate-400 dark:text-slate-500">
            &copy; {{ new Date().getFullYear() }} E-RAPOR. All rights reserved.
        </p>
    </div>
</template>
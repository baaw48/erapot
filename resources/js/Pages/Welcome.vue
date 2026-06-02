<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const isDarkMode = ref(false);

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

onMounted(() => {
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true' || (!savedDarkMode && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <Head title="Selamat Datang - E-Rapor ASTS" />

    <!-- LIGHT MODE BACKGROUND -->
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50 relative overflow-hidden">

        <!-- Dark Mode Toggle Button -->
        <button
            @click="toggleDarkMode"
            class="fixed top-4 right-4 z-50 p-3 rounded-full bg-white shadow-lg border border-slate-200 hover:shadow-xl transition-all duration-300 group"
        >
            <!-- Moon icon for light mode -->
            <svg v-if="!isDarkMode" class="w-6 h-6 text-slate-700 group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <!-- Sun icon for dark mode -->
            <svg v-else class="w-6 h-6 text-amber-500 group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-20 w-72 h-72 bg-blue-400/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 -right-20 w-72 h-72 bg-purple-400/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <!-- Main Card -->
        <div class="relative z-10 w-full max-w-3xl mx-6">

            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url" class="mb-6">
                    <img :src="$page.props.sekolah.logo_url" alt="Logo Sekolah" class="w-24 h-24 mx-auto object-contain drop-shadow-lg" />
                </div>
                <div v-else class="mb-6">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">
                    Sistem <span class="text-blue-600">E-Rapor</span> <span class="text-purple-600">ASTS</span>
                </h1>

                <p class="text-base md:text-lg text-slate-600 max-w-xl mx-auto">
                    Solusi modern untuk pengelolaan raport digital bagi sekolah
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                <template v-if="$page.props.auth.user">
                    <Link :href="route('dashboard')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                        <span>Masuk Dashboard</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Login</span>
                    </Link>

                    <Link v-if="canRegister" :href="route('register')" class="px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl shadow border border-slate-300 hover:border-blue-400 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Daftar</span>
                    </Link>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-6 text-center">
            <p class="text-sm text-slate-500">
                <span v-if="$page.props.sekolah && $page.props.sekolah.nama_sekolah">{{ $page.props.sekolah.nama_sekolah }}</span>
                <span v-else>E-RAPOT</span> &copy; {{ new Date().getFullYear() }}
            </p>
        </div>
    </div>

    <!-- DARK MODE (separate background) -->
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden hidden"></div>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 0.8; }
}
.animate-pulse {
    animation: pulse 4s ease-in-out infinite;
}

/* Dark mode styles - applied when html has 'dark' class */
:global(html.dark) .min-h-screen:first-of-type {
    display: none !important;
}

:global(html.dark) .min-h-screen:last-of-type {
    display: flex !important;
}
</style>
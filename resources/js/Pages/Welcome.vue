<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
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

    <div class="min-h-screen bg-slate-100 dark:bg-slate-900 flex flex-col items-center justify-center relative overflow-hidden transition-colors duration-300">

        <!-- Toggle Button -->
        <button
            @click="toggleDarkMode"
            class="fixed top-6 right-6 z-50 p-3 rounded-2xl bg-white dark:bg-slate-800 shadow-xl border border-slate-200 dark:border-slate-700 hover:scale-110 transition-transform duration-300"
        >
            <!-- Moon - to dark -->
            <svg v-if="!isDarkMode" class="w-6 h-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <!-- Sun - to light -->
            <svg v-else class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <!-- Background Decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 dark:bg-blue-600/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/10 dark:bg-purple-600/20 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Card -->
        <div class="relative z-10 w-full max-w-xl mx-6">

            <!-- Card Container -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl dark:shadow-slate-900/50 border border-slate-200 dark:border-slate-700 p-10 md:p-12 text-center">

                <!-- Logo Area -->
                <div class="mb-8">
                    <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url">
                        <img :src="$page.props.sekolah.logo_url" alt="Logo" class="w-28 h-28 mx-auto object-contain drop-shadow-md" />
                    </div>
                    <div v-else class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4 leading-tight">
                    E-Rapor <span class="text-blue-600 dark:text-blue-400">ASTS</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-lg text-slate-600 dark:text-slate-300 mb-10 max-w-md mx-auto leading-relaxed">
                    Sistem Informasi Raport Digital untuk pengelolaan nilai dan raport siswa secara modern
                </p>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <span>Masuk Dashboard</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Login Aplikasi</span>
                        </Link>

                        <Link v-if="canRegister" :href="route('register')" class="px-8 py-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl shadow border border-slate-300 dark:border-slate-600 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            <span>Daftar Baru</span>
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                <span v-if="$page.props.sekolah && $page.props.sekolah.nama_sekolah">{{ $page.props.sekolah.nama_sekolah }}</span>
                <span v-else>E-RAPOT SISTEM</span> &copy; {{ new Date().getFullYear() }}
            </p>
        </div>
    </div>
</template>
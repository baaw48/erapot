<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    sekolah: Object,
});

const isDark = ref(false);

const toggleDark = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else if (savedTheme === 'light') {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <Head title="Selamat Datang - E-Rapor Digital" />

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col items-center p-4 sm:p-6 md:p-8 relative overflow-y-auto overflow-x-hidden transition-colors duration-300"
        :class="isDark ? 'bg-slate-950' : 'bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/50'">

        <!-- Background Decoration (Isolated to prevent scrollbars) -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full blur-3xl opacity-30 transition-colors duration-300"
                :class="isDark ? 'bg-blue-600' : 'bg-blue-400'"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full blur-3xl opacity-20 transition-colors duration-300"
                :class="isDark ? 'bg-indigo-600' : 'bg-indigo-400'"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] sm:w-[600px] h-[300px] sm:h-[600px] rounded-full blur-3xl opacity-10 transition-colors duration-300"
                :class="isDark ? 'bg-purple-600' : 'bg-purple-300'"></div>
        </div>

        <!-- Dark Mode Toggle -->
        <button
            @click="toggleDark"
            class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50 w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-xl backdrop-blur-sm border"
            :class="isDark
                ? 'bg-slate-800/80 border-slate-700 text-amber-400 hover:bg-slate-700'
                : 'bg-white/80 border-slate-200 text-slate-600 hover:bg-white'"
        >
            <!-- Moon icon (show when light) -->
            <svg v-if="!isDark" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
            </svg>
            <!-- Sun icon (show when dark) -->
            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.166 17.834a.75.75 0 00-1.06 1.06l1.59 1.591a.75.75 0 001.061-1.06l-1.59-1.591zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.166 6.166a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
            </svg>
        </button>

        <!-- Main Card wrapper for proper safe centering -->
        <div class="w-full max-w-md m-auto relative z-10 py-10 sm:py-0 flex flex-col items-center">
            
            <!-- Glassmorphism Card -->
            <div class="w-full rounded-[2rem] sm:rounded-[2.5rem] p-6 sm:p-10 text-center backdrop-blur-xl border shadow-2xl transition-all duration-300 animate-card-in"
                :class="isDark
                    ? 'bg-slate-800/70 border-slate-700/60 shadow-black/40'
                    : 'bg-white/80 border-white/60 shadow-blue-100/50'">

                <!-- Logo / Icon -->
                <div class="mb-6 sm:mb-8 flex justify-center">
                    <div v-if="sekolah && sekolah.logo_url"
                        class="w-28 h-28 sm:w-36 sm:h-36 flex items-center justify-center">
                        <img :src="sekolah.logo_url" alt="Logo Sekolah" class="w-full h-full object-contain filter drop-shadow-2xl">
                    </div>
                    <div v-else
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl sm:rounded-3xl flex items-center justify-center shadow-xl shadow-blue-500/30"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>

                <!-- App Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-5 border"
                    :class="isDark
                        ? 'bg-blue-900/40 text-blue-400 border-blue-800/50'
                        : 'bg-blue-50 text-blue-600 border-blue-100'">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Sistem Raport Digital
                </div>

                <!-- School Name -->
                <h1 class="text-2xl sm:text-3xl font-black mb-2 leading-tight transition-colors duration-300"
                    :class="isDark ? 'text-white' : 'text-slate-900'">
                    {{ sekolah && sekolah.nama_sekolah ? sekolah.nama_sekolah : 'E-Rapor Digital' }}
                </h1>

                <!-- Subtitle -->
                <p class="text-xs sm:text-sm font-semibold mb-6 sm:mb-8 transition-colors duration-300"
                    :class="isDark ? 'text-slate-400' : 'text-slate-500'">
                    Selamat datang! Silakan masuk untuk melanjutkan.
                </p>

                <!-- Divider -->
                <div class="flex items-center gap-3 mb-6 sm:mb-8">
                    <div class="flex-1 h-px transition-colors duration-300"
                        :class="isDark ? 'bg-slate-700' : 'bg-slate-100'"></div>
                    <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest transition-colors duration-300"
                        :class="isDark ? 'text-slate-600' : 'text-slate-300'">masuk dengan akun</span>
                    <div class="flex-1 h-px transition-colors duration-300"
                        :class="isDark ? 'bg-slate-700' : 'bg-slate-100'"></div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Link :href="route('dashboard')"
                            class="w-full px-6 py-3.5 sm:px-8 sm:py-4 text-white font-bold rounded-xl sm:rounded-2xl transition-all text-sm sm:text-base flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 active:translate-y-0"
                            style="background: linear-gradient(135deg, #3b82f6, #6366f1);">
                            <span>Masuk Dashboard</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')"
                            class="w-full px-6 py-3.5 sm:px-8 sm:py-4 text-white font-bold rounded-xl sm:rounded-2xl transition-all text-sm sm:text-base flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 active:translate-y-0"
                            style="background: linear-gradient(135deg, #3b82f6, #6366f1);">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Login ke Sistem</span>
                        </Link>

                        <Link v-if="canRegister" :href="route('register')"
                            class="w-full px-6 py-3.5 sm:px-8 sm:py-4 font-bold rounded-xl sm:rounded-2xl transition-all text-sm sm:text-base flex items-center justify-center gap-2 border hover:-translate-y-0.5 active:translate-y-0"
                            :class="isDark
                                ? 'bg-slate-700/50 border-slate-600 text-slate-200 hover:bg-slate-700'
                                : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100'">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            <span>Daftar Akun</span>
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Feature Pills -->
            <div class="flex items-center justify-center gap-3 mt-6 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold backdrop-blur-sm border transition-colors duration-300"
                    :class="isDark ? 'bg-slate-800/60 border-slate-700/50 text-slate-400' : 'bg-white/70 border-slate-200/70 text-slate-500'">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Raport Digital
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold backdrop-blur-sm border transition-colors duration-300"
                    :class="isDark ? 'bg-slate-800/60 border-slate-700/50 text-slate-400' : 'bg-white/70 border-slate-200/70 text-slate-500'">
                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Aman & Terpercaya
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold backdrop-blur-sm border transition-colors duration-300"
                    :class="isDark ? 'bg-slate-800/60 border-slate-700/50 text-slate-400' : 'bg-white/70 border-slate-200/70 text-slate-500'">
                    <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Cepat & Modern
                </span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-auto pt-8 pb-4 text-center">
            <p class="text-xs font-medium relative z-10 transition-colors duration-300"
                :class="isDark ? 'text-slate-600' : 'text-slate-400'">
                &copy; {{ new Date().getFullYear() }} {{ sekolah?.nama_sekolah || 'E-Rapor Digital' }} &mdash; All rights reserved.
            </p>
        </div>
    </div>
</template>

<style scoped>
.animate-card-in {
    animation: cardIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes cardIn {
    0% { opacity: 0; transform: translateY(30px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
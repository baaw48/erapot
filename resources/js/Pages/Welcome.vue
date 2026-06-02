<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
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
    <Head title="Selamat Datang - E-Rapor ASTS" />

    <!-- Container - White for light, Black for dark -->
    <div :style="{ backgroundColor: isDark ? '#000000' : '#ffffff' }" class="min-h-screen flex flex-col items-center justify-center p-8">

        <!-- Toggle Button -->
        <button
            @click="toggleDark"
            class="fixed top-6 right-6 z-50 w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-110"
            :style="{ backgroundColor: isDark ? '#fbbf24' : '#1f2937' }"
        >
            <!-- Moon icon (light mode) -->
            <svg v-if="!isDark" class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
            </svg>
            <!-- Sun icon (dark mode) -->
            <svg v-else class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.834a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59z"/>
            </svg>
        </button>

        <!-- Main Card -->
        <div class="w-full max-w-xl rounded-3xl p-12 text-center border-4"
            :style="{
                backgroundColor: isDark ? '#18181b' : '#ffffff',
                borderColor: isDark ? '#3f3f46' : '#3b82f6',
                boxShadow: isDark ? '0 0 40px rgba(255,255,255,0.1)' : '0 25px 50px -12px rgba(0,0,0,0.25)'
            }">

            <!-- Logo -->
            <div class="mb-8">
                <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url">
                    <img :src="$page.props.sekolah.logo_url" alt="Logo" class="w-28 h-28 mx-auto object-contain" />
                </div>
                <div v-else class="w-24 h-24 mx-auto rounded-2xl flex items-center justify-center" :style="{ backgroundColor: '#3b82f6' }">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>

            <!-- Title - E-Rapor -->
            <div class="text-5xl md:text-6xl font-black mb-4" :style="{ color: isDark ? '#ffffff' : '#111827' }">
                E-Rapor
            </div>

            <!-- Title - ASTS -->
            <div class="text-5xl md:text-6xl font-black mb-8" :style="{ color: '#3b82f6' }">
                ASTS
            </div>

            <!-- Subtitle -->
            <div class="text-xl mb-12" :style="{ color: isDark ? '#a1a1aa' : '#4b5563' }">
                Sistem Informasi Raport Digital
            </div>

            <!-- Login Button -->
            <template v-if="$page.props.auth.user">
                <Link :href="route('dashboard')" class="inline-block px-10 py-4 text-white font-bold rounded-xl transition-all text-lg"
                    style="backgroundColor: '#2563eb'; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3);">
                    Masuk Dashboard
                </Link>
            </template>
            <template v-else>
                <Link :href="route('login')" class="inline-block px-10 py-4 text-white font-bold rounded-xl transition-all text-lg"
                    style="backgroundColor: '#2563eb'; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3);">
                    Login Aplikasi
                </Link>
            </template>
        </div>

        <!-- Footer -->
        <div class="mt-10 text-center" :style="{ color: isDark ? '#71717a' : '#6b7280' }">
            <span v-if="$page.props.sekolah && $page.props.sekolah.nama_sekolah">{{ $page.props.sekolah.nama_sekolah }}</span>
            <span v-else>E-RAPOT</span> &copy; {{ new Date().getFullYear() }}
        </div>
    </div>
</template>
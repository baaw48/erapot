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

    <!-- BACKGROUND - Light Mode: White, Dark Mode: Black -->
    <div :class="isDark ? 'bg-black' : 'bg-white'" class="min-h-screen flex flex-col items-center justify-center p-6">

        <!-- TOGGLE BUTTON - Fixed top right -->
        <button
            @click="toggleDark"
            class="fixed top-6 right-6 z-50 p-4 rounded-full shadow-2xl transition-transform hover:scale-110"
            :class="isDark ? 'bg-yellow-500' : 'bg-slate-900'"
        >
            <!-- Moon (light mode shows moon to switch to dark) -->
            <svg v-if="!isDark" class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <!-- Sun (dark mode shows sun to switch to light) -->
            <svg v-else class="w-7 h-7 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </button>

        <!-- MAIN CARD - Light: White with shadow, Dark: Dark gray with border -->
        <div class="w-full max-w-2xl rounded-3xl p-10 md:p-14 text-center border-4"
            :class="isDark
                ? 'bg-zinc-900 border-zinc-700'
                : 'bg-white border-blue-200 shadow-2xl shadow-blue-500/20'">

            <!-- LOGO -->
            <div class="mb-10">
                <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url">
                    <img :src="$page.props.sekolah.logo_url" alt="Logo" class="w-32 h-32 mx-auto object-contain drop-shadow-xl" />
                </div>
                <div v-else class="w-28 h-28 mx-auto rounded-2xl flex items-center justify-center shadow-xl"
                    :class="isDark ? 'bg-blue-600' : 'bg-gradient-to-br from-blue-600 to-purple-600'">
                    <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>

            <!-- TITLE - Very clear colors -->
            <h1 class="text-5xl md:text-6xl font-black mb-4">
                <span v-if="isDark" class="text-white">E-Rapor</span>
                <span v-else class="text-slate-900">E-Rapor</span>
                <span v-if="isDark" class="text-blue-400"> ASTS</span>
                <span v-else class="text-blue-600"> ASTS</span>
            </h1>

            <!-- SUBTITLE -->
            <p class="text-xl md:text-2xl mb-12 font-medium">
                <span v-if="isDark" class="text-zinc-300">Sistem Informasi Raport Digital</span>
                <span v-else class="text-slate-600">Sistem Informasi Raport Digital</span>
            </p>

            <!-- BUTTONS -->
            <div class="flex flex-col sm:flex-row justify-center gap-5">
                <template v-if="$page.props.auth.user">
                    <Link :href="route('dashboard')" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 text-lg">
                        <span>Masuk Dashboard</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 text-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Login</span>
                    </Link>
                    <Link v-if="canRegister" :href="route('register')" class="px-10 py-4 font-bold rounded-xl border-2 transition-all flex items-center justify-center gap-3 text-lg"
                        :class="isDark
                            ? 'bg-zinc-800 hover:bg-zinc-700 text-white border-zinc-600'
                            : 'bg-white hover:bg-slate-50 text-slate-700 border-slate-300 hover:border-blue-400'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Daftar</span>
                    </Link>
                </template>
            </div>
        </div>

        <!-- FOOTER -->
        <p class="mt-10 text-base font-medium"
            :class="isDark ? 'text-zinc-500' : 'text-slate-500'">
            <span v-if="$page.props.sekolah && $page.props.sekolah.nama_sekolah">{{ $page.props.sekolah.nama_sekolah }}</span>
            <span v-else>E-RAPOT</span> &copy; {{ new Date().getFullYear() }}
        </p>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarLink from '@/Components/SidebarLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const isSidebarCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);
const isDarkMode = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

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

const checkWindowSize = () => {
    if (window.innerWidth >= 768) {
        isMobileSidebarOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('resize', checkWindowSize);
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true' || (!savedDarkMode && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }
});

onUnmounted(() => {
    window.removeEventListener('resize', checkWindowSize);
});

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name || 'User');
const userRole = computed(() => page.props.auth?.user?.role || 'guest');
const userInitial = computed(() => userName.value.charAt(0).toUpperCase());
const tahunAktif = computed(() => page.props.tahun_aktif);
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-slate-50 dark:bg-slate-900 font-sans transition-colors duration-300">
        <!-- Mobile Backdrop -->
        <div
            v-if="isMobileSidebarOpen"
            @click="toggleMobileSidebar"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden transition-opacity duration-300"
        ></div>

        <!-- SIDEBAR -->
        <aside
            class="flex flex-col z-50 transition-all duration-300 ease-out relative bg-white/95 dark:bg-slate-950/95 backdrop-blur-xl border-r border-slate-200 dark:border-white/5 shadow-2xl"
            :class="[
                isSidebarCollapsed ? 'w-[72px]' : 'w-[260px]',
                isMobileSidebarOpen ? 'fixed inset-y-0 left-0 translate-x-0' : 'fixed inset-y-0 left-0 -translate-x-full md:relative md:translate-x-0'
            ]"
        >
            <!-- Brand Header -->
            <div class="h-[64px] flex items-center px-4 shrink-0 border-b border-white/10" :class="isSidebarCollapsed ? 'justify-center' : 'gap-3'">
                <Link :href="route('dashboard')" class="flex items-center" :class="isSidebarCollapsed ? 'justify-center w-full' : 'gap-3'">
                    <div class="p-2 bg-white/10 rounded-lg flex items-center justify-center border border-white/10">
                        <template v-if="$page.props.sekolah && $page.props.sekolah.logo_url">
                            <img :src="$page.props.sekolah.logo_url" alt="Logo" class="h-6 w-6 object-contain" />
                        </template>
                        <svg v-else class="h-5 w-5 text-primary-600 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div v-if="!isSidebarCollapsed" class="flex flex-col">
                        <span class="font-bold text-slate-800 dark:text-white text-base">E-RAPOR</span>
                        <span class="text-[10px] text-slate-500 dark:text-white/50 font-medium uppercase tracking-wider">Digital System</span>
                    </div>
                </Link>
            </div>

            <!-- User Info Card -->
            <div class="px-3 py-4 shrink-0">
                <div class="rounded-xl bg-white/5 border border-white/10 p-3 hover:bg-white/10 transition-colors cursor-pointer group shadow-inner" :class="isSidebarCollapsed ? 'text-center' : ''">
                    <div class="flex items-center" :class="isSidebarCollapsed ? 'justify-center' : 'gap-3'">
                        <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center font-bold text-white text-sm shadow-lg shadow-primary-500/30 group-hover:scale-105 transition-transform duration-300">
                            {{ userInitial }}
                        </div>
                        <div v-if="!isSidebarCollapsed" class="overflow-hidden min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ userName }}</p>
                            <p class="text-[11px] text-primary-600 dark:text-primary-300 font-medium uppercase tracking-wider truncate">{{ userRole }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-grow px-3 py-2 overflow-y-auto scrollbar-hide space-y-0.5">
                <div v-if="!isSidebarCollapsed" class="px-3 pt-2 pb-3 text-[10px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">
                    Menu Utama
                </div>

                <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </template>
                    Beranda
                </SidebarLink>

                <div v-if="!isSidebarCollapsed && userRole === 'admin'" class="px-3 pt-5 pb-2 text-[10px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">
                    Data Master
                </div>

                <SidebarLink v-if="userRole === 'admin'" :href="route('siswa.index')" :active="route().current('siswa.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </template>
                    Data Siswa
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('guru.index')" :active="route().current('guru.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </template>
                    Kelola Guru & User
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('kelas.index')" :active="route().current('kelas.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </template>
                    Data Kelas
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('mapel.index')" :active="route().current('mapel.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </template>
                    Mata Pelajaran
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('pembelajaran.index')" :active="route().current('pembelajaran.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                    </template>
                    Penugasan Mengajar
                </SidebarLink>

                <div v-if="!isSidebarCollapsed" class="px-3 pt-5 pb-2 text-[10px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">
                    Akademik & Penilaian
                </div>

                <SidebarLink v-if="userRole === 'admin'" :href="route('nilai.index')" :active="route().current('nilai.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </template>
                    Leger Nilai
                </SidebarLink>

                <SidebarLink v-if="userRole === 'guru'" :href="route('nilai.index')" :active="route().current('nilai.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </template>
                    Input Nilai
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('kehadiran.index')" :active="route().current('kehadiran.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </template>
                    Kehadiran & Ekskul
                </SidebarLink>

                <SidebarLink v-if="userRole === 'guru' && $page.props.auth.user.kelas_diampu" :href="route('kehadiran.index')" :active="route().current('kehadiran.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                    Input Kehadiran
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin' || (userRole === 'guru' && $page.props.auth.user.kelas_diampu)" :href="route('cetak.index')" :active="route().current('cetak.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </template>
                    Cetak Rapor
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin' || (userRole === 'guru' && $page.props.auth.user.kelas_diampu)" :href="route('arsip.index')" :active="route().current('arsip.*')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </template>
                    Arsip Rapor
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('kenaikan.index')" :active="route().current('kenaikan.index')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </template>
                    Kenaikan Kelas
                </SidebarLink>

                <div v-if="!isSidebarCollapsed && userRole === 'admin'" class="px-3 pt-5 pb-2 text-[10px] font-bold text-slate-400 dark:text-white/40 uppercase tracking-widest">
                    Sistem
                </div>

                <SidebarLink v-if="userRole === 'admin'" :href="route('sekolah.edit')" :active="route().current('sekolah.edit')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    </template>
                    Pengaturan
                </SidebarLink>

                <SidebarLink v-if="userRole === 'admin'" :href="route('admin.index')" :active="route().current('admin.*')" :isCollapsed="isSidebarCollapsed">
                    <template #icon>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/></svg>
                    </template>
                    Manajemen Admin
                </SidebarLink>
            </nav>

            <!-- Logout -->
            <div class="p-3 shrink-0 border-t border-slate-200 dark:border-white/10 mt-auto">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center justify-center py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:text-danger-600 hover:bg-danger-50 dark:text-white/60 dark:hover:bg-danger-500 dark:hover:text-white transition-all duration-200"
                    :title="isSidebarCollapsed ? 'Keluar' : ''"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span v-if="!isSidebarCollapsed" class="ml-2">Keluar</span>
                </Link>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-grow flex flex-col min-w-0 overflow-hidden relative">
            <!-- Header -->
            <header class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/60 dark:border-slate-800/60 h-[72px] shrink-0 flex items-center justify-between px-6 lg:px-8 z-20 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu -->
                    <button
                        @click="toggleMobileSidebar"
                        class="lg:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Desktop Toggle -->
                    <button
                        @click="toggleSidebar"
                        class="hidden lg:flex p-2 rounded-lg text-slate-500 bg-slate-100 dark:bg-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all"
                    >
                        <svg class="h-5 w-5 transition-transform duration-300" :class="{'rotate-180': isSidebarCollapsed}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/>
                        </svg>
                    </button>

                    <div v-if="$slots.header" class="hidden sm:block border-l border-slate-200 dark:border-slate-700 pl-6 text-lg font-bold text-slate-800 dark:text-white">
                        <slot name="header" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Tahun Ajaran Badge -->
                    <div v-if="tahunAktif" class="hidden md:flex items-center gap-2 px-4 py-2 bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800/50 rounded-full">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                        </span>
                        <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">
                            TA. {{ tahunAktif.tahun }} ({{ tahunAktif.semester }})
                        </span>
                    </div>

                    <!-- No Tahun Aktif -->
                    <div v-else class="hidden md:flex items-center gap-2 px-4 py-2 bg-danger-50 dark:bg-danger-900/30 border border-danger-200 dark:border-danger-800/50 rounded-full">
                        <svg class="h-3.5 w-3.5 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="text-xs font-semibold text-danger-600 dark:text-danger-400">TA Belum Diatur</span>
                    </div>

                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>

                    <!-- Dark Mode Toggle -->
                    <button
                        @click="toggleDarkMode"
                        class="p-2 rounded-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        :class="isDarkMode ? 'bg-slate-700 text-yellow-400 hover:bg-slate-600' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-yellow-400 hover:bg-slate-200 dark:hover:bg-slate-600'"
                        title="Toggle Dark Mode"
                    >
                        <svg v-if="isDarkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="56">
                            <template #trigger>
                                <button type="button" class="flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-1.5 pr-4 hover:border-primary-300 dark:hover:border-primary-600 transition-all focus:outline-none focus:ring-2 focus:ring-primary-500">
                                    <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-sm shadow">
                                        {{ userInitial }}
                                    </div>
                                    <div class="flex flex-col items-start hidden sm:flex">
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 leading-tight">{{ userName }}</span>
                                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500 leading-tight">{{ $page.props.auth.user.username }}</span>
                                    </div>
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 sm:hidden">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ userName }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $page.props.auth.user.username }}</p>
                                </div>
                                <div class="py-1">
                                    <DropdownLink :href="route('profile.edit')" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Profil Saya
                                    </DropdownLink>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-danger-600 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/30 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Keluar
                                    </DropdownLink>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Mobile Header Title -->
            <div class="md:hidden bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 px-4 py-3" v-if="$slots.header">
                <div class="text-lg font-bold text-slate-800 dark:text-white">
                    <slot name="header" />
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-grow overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    width: 0px;
    height: 0px;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
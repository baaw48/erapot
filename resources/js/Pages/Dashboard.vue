<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    stats: Object,
    chart_siswa_jk: Array,
    chart_siswa_kelas: Array,
    chart_nilai_kelas: Array,
    chart_nilai_mapel: Array,
    chart_kehadiran: Object,
});

// Safely get user role
const userRole = computed(() => {
    return props.$page?.props?.auth?.user?.role || 'guest';
});

// Dynamic Greeting based on time
const greeting = ref('');
const greetingIcon = ref('');

const updateGreeting = () => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 11) {
        greeting.value = 'Selamat Pagi';
        greetingIcon.value = '🌅';
    } else if (hour >= 11 && hour < 15) {
        greeting.value = 'Selamat Siang';
        greetingIcon.value = '☀️';
    } else if (hour >= 15 && hour < 18) {
        greeting.value = 'Selamat Sore';
        greetingIcon.value = '🌇';
    } else {
        greeting.value = 'Selamat Malam';
        greetingIcon.value = '🌙';
    }
};

onMounted(() => {
    updateGreeting();
});

// Safe getters for stats
const totalKelas = computed(() => props.stats?.total_kelas ?? 0);
const totalSiswa = computed(() => props.stats?.total_siswa ?? 0);
const totalMapel = computed(() => props.stats?.total_mapel ?? 0);
const tahunAktif = computed(() => props.stats?.tahun_aktif ?? null);

// Gender data
const lakiCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'L');
    return item ? Number(item.total) : 0;
});

const perempuanCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'P');
    return item ? Number(item.total) : 0;
});

const totalJK = computed(() => lakiCount.value + perempuanCount.value || 1);
const lakiPercent = computed(() => Math.round((lakiCount.value / totalJK.value) * 100));
const perempuanPercent = computed(() => Math.round((perempuanCount.value / totalJK.value) * 100));

// Chart max
const maxSiswaKelas = computed(() => {
    if (!props.chart_siswa_kelas?.length) return 40;
    const max = Math.max(...props.chart_siswa_kelas.map(k => Number(k.total)));
    return max || 40;
});

// Kehadiran
const sakit = computed(() => Number(props.chart_kehadiran?.sakit) || 0);
const izin = computed(() => Number(props.chart_kehadiran?.izin) || 0);
const alpa = computed(() => Number(props.chart_kehadiran?.alpa) || 0);
const totalHadir = computed(() => totalSiswa.value - sakit.value - izin.value - alpa.value);
const totalKehadiran = computed(() => totalSiswa.value || 1);

// Circular progress calculation
const strokeDasharray = 283; // 2 * pi * r (r=45)
const calcOffset = (percent) => strokeDasharray - (strokeDasharray * percent) / 100;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-primary-100 to-indigo-100 dark:from-primary-900/50 dark:to-indigo-900/50 rounded-xl shadow-inner">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300">Dashboard Interaktif</h2>
                    <p class="text-xs font-medium dark:text-slate-400 dark:text-slate-400 uppercase tracking-widest">Pusat Informasi</p>
                </div>
            </div>
        </template>

        <div class="space-y-8 animate-fade-in-up">
            
            <!-- Dynamic Welcome Banner (Mesh Gradient) -->
            <div class="relative overflow-hidden rounded-[2rem] bg-slate-900 shadow-2xl group">
                <!-- Abstract Mesh Background -->
                <div class="absolute inset-0 opacity-60">
                    <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-500 rounded-full mix-blend-multiply filter blur-[80px] animate-blob"></div>
                    <div class="absolute top-0 -right-4 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-[80px] animate-blob animation-delay-2000"></div>
                    <div class="absolute -bottom-24 left-32 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-[80px] animate-blob animation-delay-4000"></div>
                </div>
                
                <div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-sm font-medium mb-4">
                            <span>{{ greetingIcon }}</span>
                            <span>{{ greeting }}</span>
                        </div>
                        <h3 class="text-3xl md:text-5xl font-black text-white mb-2 tracking-tight">
                            Halo, {{ $page.props.auth.user.name }}!
                        </h3>
                        <p class="text-slate-300 text-lg md:text-xl font-medium max-w-xl leading-relaxed">
                            Selamat datang kembali di Sistem E-Rapor. Mari kita kelola data akademik dengan lebih cepat dan mudah hari ini.
                        </p>
                    </div>
                    <div class="hidden lg:flex p-6 bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-[0_0_40px_rgba(255,255,255,0.1)] transform group-hover:scale-105 group-hover:rotate-2 transition-all duration-500">
                        <svg class="w-24 h-24 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- TA Alert -->
            <div v-if="!tahunAktif" class="bg-gradient-to-r from-amber-100 to-yellow-100 dark:from-amber-900/40 dark:to-yellow-900/40 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 shadow-sm flex items-center gap-4 animate-pulse">
                <div class="p-2 bg-amber-500 text-white rounded-full">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-amber-900 dark:text-amber-200 text-sm font-semibold">
                    Tahun ajaran aktif belum diatur! Fitur penilaian mungkin tidak berfungsi dengan baik. Silakan hubungi Admin atau atur di menu Pengaturan.
                </p>
            </div>

            <!-- Quick Actions -->
            <div>
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Aksi Cepat</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Link v-if="userRole === 'admin'" :href="route('siswa.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-primary-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Kelola Siswa</span>
                    </Link>

                    <Link v-if="userRole === 'admin'" :href="route('guru.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-indigo-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Data Guru</span>
                    </Link>

                    <Link v-if="userRole === 'admin'" :href="route('kelas.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-emerald-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Data Kelas</span>
                    </Link>

                    <Link v-if="userRole === 'admin'" :href="route('sekolah.edit')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-purple-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Pengaturan Web</span>
                    </Link>

                    <Link v-if="userRole === 'guru'" :href="route('nilai.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-primary-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Input Nilai Siswa</span>
                    </Link>
                    
                    <Link v-if="userRole === 'guru' && $page.props.auth.user.kelas_diampu" :href="route('cetak.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-emerald-300 transition-all duration-300 group">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-3 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        </div>
                        <span class="text-sm font-semibold dark:text-slate-200 dark:text-slate-200">Cetak Rapor</span>
                    </Link>
                </div>
            </div>

            <!-- Floating Stats Grid -->
            <div>
                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Data Akademik Saat Ini</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kelas Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-200/50 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/30 transition-colors duration-700"></div>
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-slate-400 dark:dark:text-slate-400 uppercase tracking-widest mb-1">Total Kelas</p>
                                <p class="text-6xl font-black dark:text-white dark:text-white tracking-tighter">{{ totalKelas }}</p>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg shadow-blue-500/40 text-white transform group-hover:rotate-12 transition-transform duration-500">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Siswa Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-200/50 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/30 transition-colors duration-700"></div>
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-slate-400 dark:dark:text-slate-400 uppercase tracking-widest mb-1">Siswa Aktif</p>
                                <p class="text-6xl font-black dark:text-white dark:text-white tracking-tighter">{{ totalSiswa }}</p>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl shadow-lg shadow-emerald-500/40 text-white transform group-hover:rotate-12 transition-transform duration-500">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Mapel Card -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-slate-200/50 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl group-hover:bg-purple-500/30 transition-colors duration-700"></div>
                        <div class="relative z-10 flex justify-between items-start">
                            <div>
                                <p class="text-sm font-bold text-slate-400 dark:dark:text-slate-400 uppercase tracking-widest mb-1">Mata Pelajaran</p>
                                <p class="text-6xl font-black dark:text-white dark:text-white tracking-tighter">{{ totalMapel }}</p>
                            </div>
                            <div class="p-4 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-2xl shadow-lg shadow-purple-500/40 text-white transform group-hover:-rotate-12 transition-transform duration-500">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualizations Area -->
            <div v-if="userRole === 'admin'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Advanced Gender Circular Progress -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200/50 dark:border-slate-700/50">
                    <h4 class="font-bold dark:text-white dark:text-white text-lg mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg></span>
                        Distribusi Gender
                    </h4>
                    
                    <div class="flex items-center justify-around mt-4">
                        <!-- Laki-laki Ring -->
                        <div class="relative flex flex-col items-center">
                            <div class="relative w-32 h-32">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle class="text-slate-100 dark:dark:text-slate-200" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                    <circle class="text-blue-500 transition-all duration-1000 ease-out" stroke-width="10" :stroke-dasharray="strokeDasharray" :stroke-dashoffset="calcOffset(lakiPercent)" stroke-linecap="round" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black dark:text-white dark:text-white">{{ lakiPercent }}%</span>
                                    <span class="text-xs font-bold text-slate-400">Laki-laki</span>
                                </div>
                            </div>
                            <p class="mt-4 text-sm font-semibold dark:text-slate-400 dark:text-slate-300">Total: {{ lakiCount }}</p>
                        </div>
                        
                        <!-- Perempuan Ring -->
                        <div class="relative flex flex-col items-center">
                            <div class="relative w-32 h-32">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle class="text-slate-100 dark:dark:text-slate-200" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                    <circle class="text-pink-500 transition-all duration-1000 ease-out delay-300" stroke-width="10" :stroke-dasharray="strokeDasharray" :stroke-dashoffset="calcOffset(perempuanPercent)" stroke-linecap="round" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-black dark:text-white dark:text-white">{{ perempuanPercent }}%</span>
                                    <span class="text-xs font-bold text-slate-400">Perempuan</span>
                                </div>
                            </div>
                            <p class="mt-4 text-sm font-semibold dark:text-slate-400 dark:text-slate-300">Total: {{ perempuanCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Modern Attendance Bars -->
                <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-slate-200/50 dark:border-slate-700/50">
                    <h4 class="font-bold dark:text-white dark:text-white text-lg mb-6 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                        Statistik Kehadiran
                    </h4>
                    
                    <div class="space-y-6">
                        <!-- Hadir -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold dark:text-slate-200 dark:text-slate-300">Hadir</span>
                                <span class="text-sm font-bold text-emerald-600">{{ totalHadir }} Siswa</span>
                            </div>
                            <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all duration-1000 ease-out" :style="`width: ${(totalHadir / totalKehadiran) * 100}%`"></div>
                            </div>
                        </div>

                        <!-- Izin/Sakit -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold dark:text-slate-200 dark:text-slate-300">Izin & Sakit</span>
                                <span class="text-sm font-bold text-amber-500">{{ sakit + izin }} Siswa</span>
                            </div>
                            <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all duration-1000 ease-out delay-150" :style="`width: ${((sakit + izin) / totalKehadiran) * 100}%`"></div>
                            </div>
                        </div>

                        <!-- Alpa -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold dark:text-slate-200 dark:text-slate-300">Alpa</span>
                                <span class="text-sm font-bold text-red-500">{{ alpa }} Siswa</span>
                            </div>
                            <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-red-400 to-red-500 rounded-full transition-all duration-1000 ease-out delay-300" :style="`width: ${(alpa / totalKehadiran) * 100}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-blob {
    animation: blob 7s infinite alternate ease-in-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
</style>
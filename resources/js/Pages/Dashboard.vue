<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    stats: Object,
    chart_siswa_jk: Array,
    chart_siswa_kelas: Array,
    chart_nilai_kelas: Array,
    chart_nilai_mapel: Array,
    chart_kehadiran: Object,
    mapel_diampu: Array,
});

// Fix: gunakan usePage() untuk ambil data user dengan benar
const page = usePage();
const userRole = computed(() => page.props.auth?.user?.role || 'guest');
const userName = computed(() => page.props.auth?.user?.name || 'Pengguna');
const kelasDiampu = computed(() => page.props.auth?.user?.kelas_diampu || null);
const sekolah = computed(() => page.props.sekolah);

// Dynamic Greeting based on time
const greeting = ref('');
const greetingIcon = ref('');
const updateGreeting = () => {
    const hour = new Date().getHours();
    if (hour >= 5 && hour < 11)      { greeting.value = 'Selamat Pagi';  greetingIcon.value = '🌅'; }
    else if (hour >= 11 && hour < 15) { greeting.value = 'Selamat Siang'; greetingIcon.value = '☀️'; }
    else if (hour >= 15 && hour < 18) { greeting.value = 'Selamat Sore';  greetingIcon.value = '🌇'; }
    else                              { greeting.value = 'Selamat Malam'; greetingIcon.value = '🌙'; }
};
onMounted(() => updateGreeting());

// Safe getters for stats
const totalKelas  = computed(() => props.stats?.total_kelas  ?? 0);
const totalSiswa  = computed(() => props.stats?.total_siswa  ?? 0);
const totalMapel  = computed(() => props.stats?.total_mapel  ?? 0);
const tahunAktif  = computed(() => props.stats?.tahun_aktif  ?? null);
const totalSiswaWali = computed(() => props.stats?.total_siswa_wali ?? 0);

// Gender data
const lakiCount      = computed(() => Number(props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'L')?.total) || 0);
const perempuanCount = computed(() => Number(props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'P')?.total) || 0);
const totalJK        = computed(() => lakiCount.value + perempuanCount.value || 1);
const lakiPercent    = computed(() => Math.round((lakiCount.value / totalJK.value) * 100));
const perempuanPercent = computed(() => Math.round((perempuanCount.value / totalJK.value) * 100));

// Siswa per kelas chart
const maxSiswaKelas = computed(() => {
    if (!props.chart_siswa_kelas?.length) return 40;
    return Math.max(...props.chart_siswa_kelas.map(k => Number(k.total))) || 40;
});

// Nilai per kelas chart
const maxNilaiKelas = computed(() => {
    if (!props.chart_nilai_kelas?.length) return 100;
    return Math.max(...props.chart_nilai_kelas.map(k => Number(k.rata_rata))) || 100;
});

// Kehadiran
const sakit         = computed(() => Number(props.chart_kehadiran?.sakit) || 0);
const izin          = computed(() => Number(props.chart_kehadiran?.izin)  || 0);
const alpa          = computed(() => Number(props.chart_kehadiran?.alpa)  || 0);
const totalAbsen    = computed(() => sakit.value + izin.value + alpa.value);
const totalHadir    = computed(() => Math.max(0, totalSiswa.value - totalAbsen.value));
const totalKehadiran = computed(() => totalSiswa.value || 1);

// Circular progress
const strokeDasharray = 283;
const calcOffset = (percent) => strokeDasharray - (strokeDasharray * percent) / 100;

// Color for grade bar
const gradeColor = (nilai) => {
    const n = Number(nilai);
    if (n >= 85) return 'from-emerald-400 to-emerald-500';
    if (n >= 70) return 'from-blue-400 to-blue-500';
    if (n >= 55) return 'from-amber-400 to-amber-500';
    return 'from-rose-400 to-rose-500';
};
</script>

<template>
    <Head title="Dashboard">
        <link v-if="sekolah && sekolah.logo_url" rel="icon" type="image/png" :href="sekolah.logo_url" />
        <link v-else rel="icon" type="image/x-icon" href="/favicon.ico" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-primary-100 to-indigo-100 dark:from-primary-900/50 dark:to-indigo-900/50 rounded-xl shadow-inner">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600 dark:from-white dark:to-slate-300">Dashboard</h2>
                    <p class="text-xs font-medium text-slate-400 dark:text-slate-500 uppercase tracking-widest">Pusat Informasi Akademik</p>
                </div>
            </div>
        </template>

        <div class="space-y-6 pb-10 animate-fade-in-up">

            <!-- TA Alert -->
            <div v-if="!tahunAktif" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-2 bg-amber-500 text-white rounded-full shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-amber-900 dark:text-amber-200 text-sm font-semibold">Tahun ajaran aktif belum diatur! Silakan atur di menu <strong>Pengaturan → Tahun Pelajaran</strong>.</p>
            </div>

            <!-- Welcome Banner -->
            <div class="relative overflow-hidden rounded-[2rem] bg-slate-50 dark:bg-slate-900 shadow-xl border border-slate-200 dark:border-transparent group">
                <div class="absolute inset-0 opacity-40 dark:opacity-60">
                    <div class="absolute -top-24 -left-24 w-80 h-80 bg-primary-400 dark:bg-primary-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob"></div>
                    <div class="absolute top-0 -right-4 w-80 h-80 bg-indigo-400 dark:bg-indigo-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob animation-delay-2000"></div>
                    <div class="absolute -bottom-24 left-32 w-80 h-80 bg-purple-400 dark:bg-purple-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob animation-delay-4000"></div>
                </div>
                <div class="relative z-10 p-6 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/60 dark:bg-white/10 backdrop-blur-md border border-slate-200 dark:border-white/20 text-slate-800 dark:text-white text-sm font-bold mb-3 shadow-sm">
                            <span>{{ greetingIcon }}</span><span>{{ greeting }}</span>
                        </div>
                        <h3 class="text-2xl md:text-4xl font-black text-slate-900 dark:text-white mb-2 tracking-tight">
                            Halo, {{ userName }}!
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm md:text-base font-medium max-w-xl leading-relaxed">
                            Selamat datang kembali di Sistem E-Rapor.
                            <span v-if="tahunAktif"> Tahun Ajaran <strong class="text-primary-600 dark:text-primary-400">{{ tahunAktif.nama_tahun }}</strong> sedang aktif.</span>
                        </p>
                    </div>
                    <div class="hidden md:flex p-5 bg-white/60 dark:bg-white/10 backdrop-blur-xl border border-white/40 dark:border-white/20 rounded-3xl shadow-lg transform group-hover:scale-105 group-hover:rotate-2 transition-all duration-500">
                        <svg class="w-20 h-20 text-primary-500 dark:text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ======================== STATS GRID ======================== -->
            <div>
                <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Data Akademik</h4>

                <!-- ADMIN stats -->
                <div v-if="userRole === 'admin'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kelas -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Rombel</p>
                                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalKelas }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Total Kelas</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30 text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Siswa -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Aktif</p>
                                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalSiswa }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Siswa</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/30 text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Mapel -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Studi</p>
                                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalMapel }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Mata Pelajaran</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl shadow-lg shadow-purple-500/30 text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tahun Aktif -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100 dark:border-slate-700/50 group relative overflow-hidden">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div class="min-w-0 flex-1 pr-2">
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">TA Aktif</p>
                                <p v-if="tahunAktif" class="text-base font-black text-slate-900 dark:text-white leading-tight">{{ tahunAktif.nama_tahun }}</p>
                                <p v-else class="text-sm font-black text-rose-500">Belum diatur</p>
                                <p v-if="tahunAktif" class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1 capitalize">Semester {{ tahunAktif.semester }}</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg shadow-amber-500/30 text-white shrink-0">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GURU stats -->
                <div v-if="userRole === 'guru'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Kelas Saya</p>
                                <p class="text-2xl font-black text-slate-900 dark:text-white">{{ kelasDiampu || '—' }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Kelas Diampu</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl text-white shadow-lg shadow-emerald-500/30">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                        <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/25 transition-colors duration-500"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Wali Kelas</p>
                                <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalSiswaWali }}</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Siswa</p>
                            </div>
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-blue-500/30">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Mapel Diampu (GURU) -->
                <div v-if="userRole === 'guru' && mapel_diampu && mapel_diampu.length > 0" class="mt-6">
                    <h4 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">Mata Pelajaran Diampu</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(mapel, idx) in mapel_diampu" :key="idx" class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-700/50 flex items-center gap-4 hover:-translate-y-1 hover:shadow-md transition-all duration-300 group">
                            <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-500 flex items-center justify-center shrink-0 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">{{ mapel.nama_mapel }}</h5>
                                <p class="text-xs font-semibold text-slate-500 mt-0.5">Kelas {{ mapel.kelas }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================== QUICK ACTIONS ======================== -->
            <div>
                <h4 class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-4">Aksi Cepat</h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <!-- Admin actions -->
                    <template v-if="userRole === 'admin'">
                        <Link :href="route('siswa.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Data Siswa</span>
                        </Link>
                        <Link :href="route('guru.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-indigo-200 dark:hover:border-indigo-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Data Guru</span>
                        </Link>
                        <Link :href="route('kelas.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-emerald-200 dark:hover:border-emerald-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Data Kelas</span>
                        </Link>
                        <Link :href="route('sekolah.edit')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-purple-200 dark:hover:border-purple-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Pengaturan</span>
                        </Link>
                    </template>

                    <!-- Guru actions -->
                    <template v-if="userRole === 'guru'">
                        <Link :href="route('nilai.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Input Nilai</span>
                        </Link>
                        <Link v-if="kelasDiampu" :href="route('cetak.index')" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-emerald-200 dark:hover:border-emerald-700 transition-all duration-300 group">
                            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-2 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 text-center">Cetak Rapor</span>
                        </Link>
                    </template>
                </div>
            </div>

            <!-- ======================== VISUALISASI (ADMIN ONLY) ======================== -->
            <template v-if="userRole === 'admin'">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Gender Distribution -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                        <h4 class="font-bold text-slate-800 dark:text-white text-base mb-6 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                            </span>
                            Distribusi Gender Siswa
                        </h4>
                        <div class="flex flex-col sm:flex-row items-center justify-around gap-6 sm:gap-0">
                            <!-- Laki-laki -->
                            <div class="flex flex-col items-center">
                                <div class="relative w-28 h-28">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                        <circle class="text-slate-100 dark:text-slate-700" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                        <circle class="text-blue-500 transition-all duration-1000 ease-out" stroke-width="10" :stroke-dasharray="strokeDasharray" :stroke-dashoffset="calcOffset(lakiPercent)" stroke-linecap="round" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-xl font-black text-slate-900 dark:text-white">{{ lakiPercent }}%</span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">Laki-laki</span>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm font-bold text-blue-600 dark:text-blue-400">{{ lakiCount }} Siswa</p>
                            </div>
                            <!-- VS divider -->
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-16 sm:w-px h-px sm:h-16 bg-slate-100 dark:bg-slate-700"></div>
                                <span class="text-xs font-black text-slate-300 dark:text-slate-600">VS</span>
                                <div class="w-16 sm:w-px h-px sm:h-16 bg-slate-100 dark:bg-slate-700"></div>
                            </div>
                            <!-- Perempuan -->
                            <div class="flex flex-col items-center">
                                <div class="relative w-28 h-28">
                                    <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100">
                                        <circle class="text-slate-100 dark:text-slate-700" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                        <circle class="text-pink-500 transition-all duration-1000 ease-out delay-300" stroke-width="10" :stroke-dasharray="strokeDasharray" :stroke-dashoffset="calcOffset(perempuanPercent)" stroke-linecap="round" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50"/>
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-xl font-black text-slate-900 dark:text-white">{{ perempuanPercent }}%</span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">Perempuan</span>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm font-bold text-pink-600 dark:text-pink-400">{{ perempuanCount }} Siswa</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kehadiran Bars -->
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                        <h4 class="font-bold text-slate-800 dark:text-white text-base mb-6 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            Rekap Kehadiran Siswa
                        </h4>
                        <div class="space-y-5">
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Hadir</span>
                                    <span class="text-sm font-black text-emerald-600">{{ totalHadir }} siswa</span>
                                </div>
                                <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all duration-1000 ease-out" :style="`width: ${(totalHadir / totalKehadiran) * 100}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Sakit</span>
                                    <span class="text-sm font-black text-blue-500">{{ sakit }} siswa</span>
                                </div>
                                <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-blue-500 rounded-full transition-all duration-1000 ease-out delay-100" :style="`width: ${(sakit / totalKehadiran) * 100}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Izin</span>
                                    <span class="text-sm font-black text-amber-500">{{ izin }} siswa</span>
                                </div>
                                <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all duration-1000 ease-out delay-200" :style="`width: ${(izin / totalKehadiran) * 100}%`"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-end mb-1.5">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Alpa</span>
                                    <span class="text-sm font-black text-rose-500">{{ alpa }} siswa</span>
                                </div>
                                <div class="h-3 w-full bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-rose-400 to-rose-500 rounded-full transition-all duration-1000 ease-out delay-300" :style="`width: ${(alpa / totalKehadiran) * 100}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Siswa per Kelas Chart -->
                <div v-if="chart_siswa_kelas && chart_siswa_kelas.length" class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                    <h4 class="font-bold text-slate-800 dark:text-white text-base mb-6 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        Jumlah Siswa per Kelas
                    </h4>
                    <div class="space-y-3">
                        <div v-for="kelas in chart_siswa_kelas" :key="kelas.nama" class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-16 shrink-0 text-right">{{ kelas.nama }}</span>
                            <div class="flex-1 h-8 bg-slate-100 dark:bg-slate-700 rounded-lg overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-400 to-blue-500 rounded-lg transition-all duration-700 ease-out flex items-center justify-end pr-2"
                                    :style="`width: ${(Number(kelas.total) / maxSiswaKelas) * 100}%`">
                                    <span class="text-[10px] font-black text-white">{{ kelas.total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nilai Rata-rata per Kelas -->
                <div v-if="chart_nilai_kelas && chart_nilai_kelas.length" class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                    <h4 class="font-bold text-slate-800 dark:text-white text-base mb-6 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </span>
                        Rata-rata Nilai per Kelas
                        <span class="ml-auto text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500">Tahun Aktif</span>
                    </h4>
                    <div class="space-y-3">
                        <div v-for="kelas in chart_nilai_kelas" :key="kelas.nama_kelas" class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-16 shrink-0 text-right">{{ kelas.nama_kelas }}</span>
                            <div class="flex-1 h-8 bg-slate-100 dark:bg-slate-700 rounded-lg overflow-hidden">
                                <div class="h-full rounded-lg transition-all duration-700 ease-out flex items-center justify-end pr-2"
                                    :class="`bg-gradient-to-r ${gradeColor(kelas.rata_rata)}`"
                                    :style="`width: ${(Number(kelas.rata_rata) / 100) * 100}%`">
                                    <span class="text-[10px] font-black text-white">{{ Number(kelas.rata_rata).toFixed(1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>≥ 85 (Sangat Baik)</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>≥ 70 (Baik)</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>≥ 55 (Cukup)</span>
                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400"><span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>&lt; 55 (Perlu Perhatian)</span>
                    </div>
                </div>
            </template>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-blob { animation: blob 7s infinite alternate ease-in-out; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
@keyframes blob {
    0%   { transform: translate(0,0) scale(1); }
    33%  { transform: translate(30px,-50px) scale(1.1); }
    66%  { transform: translate(-20px,20px) scale(0.9); }
    100% { transform: translate(0,0) scale(1); }
}
</style>
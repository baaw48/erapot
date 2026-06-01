<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

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
const totalKehadiran = computed(() => sakit.value + izin.value + alpa.value || 1);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary-100 dark:bg-primary-900/50 rounded-lg">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Dashboard</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ringkasan Sistem</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white">
                <h3 class="text-xl font-bold mb-1">
                    Selamat Datang, {{ $page.props.auth.user.name }}!
                </h3>
                <p class="text-blue-100 text-sm">
                    Role: <span class="font-semibold bg-white/20 px-2 py-0.5 rounded text-xs">{{ userRole.toUpperCase() }}</span>
                </p>
            </div>

            <!-- TA Alert -->
            <div v-if="!tahunAktif" class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <p class="text-amber-800 dark:text-amber-200 text-sm font-medium">
                    ⚠️ Tahun ajaran belum diatur. Silakan atur di Pengaturan.
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Kelas -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/60 dark:border-slate-700/60 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all duration-500"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg shadow-blue-500/30 text-white group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Kelas</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ totalKelas }}</p>
                        </div>
                    </div>
                </div>

                <!-- Siswa -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/60 dark:border-slate-700/60 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="p-4 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/30 text-white group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Siswa</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ totalSiswa }}</p>
                        </div>
                    </div>
                </div>

                <!-- Mapel -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/60 dark:border-slate-700/60 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all duration-500"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="p-4 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl shadow-lg shadow-purple-500/30 text-white group-hover:scale-110 group-hover:-rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Mata Pelajaran</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ totalMapel }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div v-if="userRole === 'admin'" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Gender Distribution -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                    <h4 class="font-semibold text-slate-800 dark:text-white mb-4">Distribusi Gender</h4>
                    <div class="flex items-center justify-around">
                        <div class="text-center">
                            <div class="w-20 h-20 rounded-full border-8 border-blue-500 flex items-center justify-center mb-2">
                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ lakiCount }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Laki-laki</p>
                            <p class="text-xs text-slate-500 dark:text-slate-500">{{ lakiPercent }}%</p>
                        </div>
                        <div class="text-center">
                            <div class="w-20 h-20 rounded-full border-8 border-pink-500 flex items-center justify-center mb-2">
                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ perempuanCount }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400">Perempuan</p>
                            <p class="text-xs text-slate-500 dark:text-slate-500">{{ perempuanPercent }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                    <h4 class="font-semibold text-slate-800 dark:text-white mb-4">Kehadiran</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 text-center">
                            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ totalHadir }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400">Hadir</p>
                        </div>
                        <div class="bg-amber-100 dark:bg-amber-900/30 rounded-lg p-3 text-center">
                            <p class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ sakit + izin }}</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">Izin/Sakit</p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900/30 rounded-lg p-3 text-center">
                            <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ alpa }}</p>
                            <p class="text-xs text-red-600 dark:text-red-400">Alpa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Siswa per Kelas -->
            <div v-if="userRole === 'admin' && chart_siswa_kelas?.length" class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                <h4 class="font-semibold text-slate-800 dark:text-white mb-4">Siswa per Kelas</h4>
                <div class="space-y-3">
                    <div v-for="kelas in chart_siswa_kelas" :key="kelas.nama" class="flex items-center gap-3">
                        <span class="w-24 text-sm text-slate-600 dark:text-slate-400 truncate">{{ kelas.nama }}</span>
                        <div class="flex-1 h-3 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-blue-500 rounded-full"
                                :style="{ width: `${(Number(kelas.total) / maxSiswaKelas) * 100}%` }"
                            ></div>
                        </div>
                        <span class="w-8 text-sm font-semibold text-slate-700 dark:text-slate-300 text-right">{{ kelas.total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
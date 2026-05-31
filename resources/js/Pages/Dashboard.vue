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

const userRole = computed(() => props.$page?.props?.auth?.user?.role || 'guest');

// Stat cards
const statCards = computed(() => [
    {
        label: 'Total Kelas',
        value: props.stats?.total_kelas || 0,
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>`,
        color: 'from-primary-500 to-primary-600',
        bgColor: 'bg-primary-50',
        textColor: 'text-primary-600',
        ringColor: 'ring-primary-100',
    },
    {
        label: 'Total Siswa',
        value: props.stats?.total_siswa || 0,
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>`,
        color: 'from-success-500 to-success-600',
        bgColor: 'bg-success-50',
        textColor: 'text-success-600',
        ringColor: 'ring-success-100',
    },
    {
        label: 'Mata Pelajaran',
        value: props.stats?.total_mapel || 0,
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>`,
        color: 'from-secondary-500 to-secondary-600',
        bgColor: 'bg-secondary-50',
        textColor: 'text-secondary-600',
        ringColor: 'ring-secondary-100',
    },
]);

// Calculate gender chart
const lakiCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'L');
    return item ? item.total : 0;
});
const perempuanCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'P');
    return item ? item.total : 0;
});
const totalJK = computed(() => lakiCount.value + perempuanCount.value || 1);
const lakiPercent = computed(() => Math.round((lakiCount.value / totalJK.value) * 100));
const perempuanPercent = computed(() => Math.round((perempuanCount.value / totalJK.value) * 100));

// Max for charts
const maxSiswaKelas = computed(() => {
    if (!props.chart_siswa_kelas || props.chart_siswa_kelas.length === 0) return 40;
    return Math.max(...props.chart_siswa_kelas.map(k => k.total)) || 40;
});

// Attendance
const kehadiranSakit = computed(() => Number(props.chart_kehadiran?.sakit || 0));
const kehadiranIzin = computed(() => Number(props.chart_kehadiran?.izin || 0));
const kehadiranAlpa = computed(() => Number(props.chart_kehadiran?.alpa || 0));
const totalKehadiran = computed(() => kehadiranSakit.value + kehadiranIzin.value + kehadiranAlpa.value || 1);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-primary-100 text-primary-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">Dashboard</h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Ringkasan Sistem E-Rapot</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Welcome Banner -->
            <div class="relative overflow-hidden bg-gradient-to-r from-primary-600 via-primary-500 to-primary-700 rounded-2xl shadow-xl">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -right-16 -top-16 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute -left-8 -bottom-8 w-48 h-48 bg-white/30 rounded-full blur-2xl"></div>
                </div>

                <div class="relative px-8 py-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2">
                                Selamat Datang, {{ $page.props.auth.user.name }}! 👋
                            </h3>
                            <p class="text-primary-100 text-sm max-w-xl">
                                Anda masuk sebagai <span class="font-semibold bg-white/20 px-2 py-0.5 rounded-md">{{ userRole.toUpperCase() }}</span>.
                                Gunakan menu di sebelah untuk mengelola data rapor.
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <div v-if="$page.props.auth.user.role === 'guru' && $page.props.auth.user.mapel_diampu" class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-[10px] font-semibold text-primary-200 uppercase tracking-wider mb-1">Mapel Diampu</p>
                                <p class="text-white font-bold">{{ $page.props.auth.user.mapel_diampu }}</p>
                            </div>
                            <div v-if="$page.props.auth.user.role === 'guru' && $page.props.auth.user.kelas_diampu" class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                <p class="text-[10px] font-semibold text-primary-200 uppercase tracking-wider mb-1">Wali Kelas</p>
                                <p class="text-white font-bold">{{ $page.props.auth.user.kelas_diampu }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TA Alert -->
            <div v-if="!stats?.tahun_aktif" class="flex items-start gap-4 bg-warning-50 border border-warning-200 p-5 rounded-2xl">
                <div class="p-2.5 bg-warning-100 rounded-xl text-warning-600 shrink-0">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-warning-800">Tahun Ajaran Belum Diset</h4>
                    <p class="text-sm text-warning-700 mt-1">
                        Silakan set tahun ajaran aktif di <strong>Pengaturan > Tahun Ajaran</strong> agar sistem dapat berjalan dengan benar.
                    </p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <template v-for="(stat, index) in statCards" :key="index">
                    <div class="relative bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full opacity-10" :class="stat.bgColor"></div>
                        <div class="flex items-center gap-5">
                            <div class="p-4 rounded-2xl bg-gradient-to-br shadow-inner" :class="[stat.bgColor, stat.textColor]">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" v-html="stat.icon"></svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ stat.label }}</p>
                                <p class="text-3xl font-bold text-slate-800 mt-1">{{ stat.value }}</p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r" :class="stat.color" style="opacity: 0; transition: opacity 0.3s;" :style="'opacity: 0.3'"></div>
                    </div>
                </template>
            </div>

            <!-- Charts Section -->
            <div v-if="userRole === 'admin' && chart_siswa_jk?.length" class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Gender Chart -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-slate-800">Distribusi Gender</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Perbandingan siswa</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center mb-6">
                        <div class="relative w-44 h-44">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e2e8f0" stroke-width="12"/>
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12"
                                    :stroke-dasharray="`${lakiPercent * 2.51} 251`" stroke-linecap="round"/>
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#8b5cf6" stroke-width="12"
                                    :stroke-dasharray="`${perempuanPercent * 2.51} 251`" :stroke-dashoffset="`-${lakiPercent * 2.51}`" stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-xs font-medium text-slate-400">Total</span>
                                <span class="text-2xl font-bold text-slate-800">{{ stats?.total_siswa }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-primary-50 rounded-xl p-4 text-center border border-primary-100">
                            <div class="flex items-center justify-center gap-1.5 mb-1">
                                <div class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                                <span class="text-xs font-medium text-primary-600">Laki-laki</span>
                            </div>
                            <span class="text-xl font-bold text-primary-700">{{ lakiCount }}</span>
                            <span class="block text-[10px] text-primary-500 mt-0.5">{{ lakiPercent }}%</span>
                        </div>
                        <div class="bg-secondary-50 rounded-xl p-4 text-center border border-secondary-100">
                            <div class="flex items-center justify-center gap-1.5 mb-1">
                                <div class="w-2.5 h-2.5 rounded-full bg-secondary-500"></div>
                                <span class="text-xs font-medium text-secondary-600">Perempuan</span>
                            </div>
                            <span class="text-xl font-bold text-secondary-700">{{ perempuanCount }}</span>
                            <span class="block text-[10px] text-secondary-500 mt-0.5">{{ perempuanPercent }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Students per Class -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-slate-800">Siswa per Kelas</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Statistik kelas</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <template v-for="(kelas, idx) in chart_siswa_kelas" :key="idx">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-slate-600 w-16 truncate">{{ kelas.nama }}</span>
                                <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-primary-500 to-primary-400 rounded-full transition-all duration-500"
                                        :style="`width: ${(kelas.total / maxSiswaKelas) * 100}%`"></div>
                                </div>
                                <span class="text-sm font-semibold text-slate-700 w-8 text-right">{{ kelas.total }}</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-slate-800">Kehadiran</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Rekap semester ini</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-success-50 rounded-xl p-3 text-center border border-success-100">
                            <svg class="w-5 h-5 text-success-500 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-lg font-bold text-success-700">{{ totalKehadiran.value - kehadiranSakit.value - kehadiranIzin.value - kehadiranAlpa.value }}</span>
                            <span class="block text-[10px] text-success-600 mt-0.5">Hadir</span>
                        </div>
                        <div class="bg-warning-50 rounded-xl p-3 text-center border border-warning-100">
                            <svg class="w-5 h-5 text-warning-500 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-lg font-bold text-warning-700">{{ kehadiranSakit.value + kehadiranIzin.value }}</span>
                            <span class="block text-[10px] text-warning-600 mt-0.5">Izin/Sakit</span>
                        </div>
                        <div class="bg-danger-50 rounded-xl p-3 text-center border border-danger-100">
                            <svg class="w-5 h-5 text-danger-500 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-lg font-bold text-danger-700">{{ kehadiranAlpa.value }}</span>
                            <span class="block text-[10px] text-danger-600 mt-0.5">Alpa</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Sakit</span>
                            <span class="font-semibold text-slate-700">{{ kehadiranSakit }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-warning-400 rounded-full" :style="`width: ${Math.round((kehadiranSakit / totalKehadiran) * 100)}%`"></div>
                        </div>
                    </div>
                    <div class="space-y-2 mt-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Izin</span>
                            <span class="font-semibold text-slate-700">{{ kehadiranIzin }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-warning-500 rounded-full" :style="`width: ${Math.round((kehadiranIzin / totalKehadiran) * 100)}%`"></div>
                        </div>
                    </div>
                    <div class="space-y-2 mt-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">Alpa</span>
                            <span class="font-semibold text-slate-700">{{ kehadiranAlpa }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-danger-500 rounded-full" :style="`width: ${Math.round((kehadiranAlpa / totalKehadiran) * 100)}%`"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Charts -->
            <div v-if="userRole === 'admin' && chart_nilai_kelas?.length" class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                <!-- Nilai per Kelas -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-slate-800">Rata-rata Nilai per Kelas</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Grafik nilai semester ini</p>
                        </div>
                        <div class="px-3 py-1 bg-primary-50 rounded-full">
                            <span class="text-xs font-medium text-primary-600">Semester Aktif</span>
                        </div>
                    </div>
                    <div class="flex items-end justify-around h-40 gap-2">
                        <template v-for="(item, idx) in chart_nilai_kelas" :key="idx">
                            <div class="flex flex-col items-center gap-2 flex-1">
                                <div class="w-full max-w-12 bg-primary-100 rounded-lg relative" :style="`height: ${(item.rata_rata / 100) * 100}%`">
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600">
                                        {{ item.rata_rata.toFixed(1) }}
                                    </div>
                                </div>
                                <span class="text-[10px] font-medium text-slate-500 truncate w-full text-center">{{ item.nama_kelas }}</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Nilai per Mapel -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-semibold text-slate-800">Rata-rata Nilai per Mapel</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Performa mata pelajaran</p>
                        </div>
                        <div class="px-3 py-1 bg-secondary-50 rounded-full">
                            <span class="text-xs font-medium text-secondary-600">Semua Mapel</span>
                        </div>
                    </div>
                    <div class="space-y-3 max-h-40 overflow-y-auto">
                        <template v-for="(item, idx) in chart_nilai_mapel" :key="idx">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-slate-600 w-28 truncate">{{ item.nama_mapel }}</span>
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-secondary-500 to-secondary-400 rounded-full" :style="`width: ${item.rata_rata}%`"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 w-10 text-right">{{ item.rata_rata.toFixed(1) }}</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
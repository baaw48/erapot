<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    kelas: String,
    tahunAktif: Object,
    totalSiswa: Number,
    totalMapel: Number,
    totalSelesai: Number,
    totalBelum: Number,
    daftarMonitoring: Array,
});

const completionPercent = computed(() => {
    if (!props.totalMapel) return 0;
    return Math.round((props.totalSelesai / props.totalMapel) * 100);
});

const statusColor = (status) => {
    if (status === 'Selesai') return 'emerald';
    if (status === 'Sebagian') return 'amber';
    return 'rose';
};

const progressColor = (persentase) => {
    if (persentase >= 100) return 'bg-emerald-500';
    if (persentase > 0) return 'bg-amber-400';
    return 'bg-rose-400';
};
</script>

<template>
    <Head title="Monitoring Nilai" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="p-2 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Monitoring Nilai</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        Kelas {{ kelas }}
                        <span v-if="tahunAktif"> — {{ tahunAktif.nama_tahun }} ({{ tahunAktif.semester === 'ganjil' ? 'Semester Ganjil' : 'Semester Genap' }})</span>
                    </p>
                </div>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Stats Summary -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Total Mapel -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                    <div class="absolute -bottom-5 -right-5 w-20 h-20 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-colors duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total Mapel</p>
                        <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalMapel }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Mata Pelajaran</p>
                    </div>
                    <div class="absolute top-4 right-4 p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl text-white shadow-lg shadow-indigo-500/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>

                <!-- Total Siswa -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                    <div class="absolute -bottom-5 -right-5 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total Siswa</p>
                        <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ totalSiswa }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Siswa Aktif</p>
                    </div>
                    <div class="absolute top-4 right-4 p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>

                <!-- Selesai -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                    <div class="absolute -bottom-5 -right-5 w-20 h-20 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Sudah Selesai</p>
                        <p class="text-4xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter">{{ totalSelesai }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Nilai Lengkap</p>
                    </div>
                    <div class="absolute top-4 right-4 p-2 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-xl text-white shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Belum -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-slate-700/50 relative overflow-hidden group">
                    <div class="absolute -bottom-5 -right-5 w-20 h-20 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-colors duration-500"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Belum Selesai</p>
                        <p class="text-4xl font-black text-rose-600 dark:text-rose-400 tracking-tighter">{{ totalBelum }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-semibold mt-1">Perlu Ditagih</p>
                    </div>
                    <div class="absolute top-4 right-4 p-2 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl text-white shadow-lg shadow-rose-500/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-slate-100 dark:border-slate-700/50">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">Progres Keseluruhan Kelas {{ kelas }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ totalSelesai }} dari {{ totalMapel }} mata pelajaran sudah lengkap nilainya</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-black" :class="completionPercent >= 100 ? 'text-emerald-500' : completionPercent > 0 ? 'text-amber-500' : 'text-rose-500'">{{ completionPercent }}%</span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-4 overflow-hidden">
                    <div
                        class="h-4 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                        :class="completionPercent >= 100 ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : completionPercent > 0 ? 'bg-gradient-to-r from-amber-400 to-orange-500' : 'bg-rose-400'"
                        :style="{ width: completionPercent + '%' }"
                    >
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Daftar Monitoring per Mapel -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700/50 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/50">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </span>
                        Detail per Mata Pelajaran
                    </h3>
                </div>

                <!-- Empty State -->
                <div v-if="!daftarMonitoring || daftarMonitoring.length === 0" class="p-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                    </div>
                    <h4 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-1">Belum Ada Penugasan</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-xs">Belum ada guru mata pelajaran yang ditugaskan untuk kelas ini. Silakan tambahkan penugasan mengajar terlebih dahulu.</p>
                </div>

                <!-- Table -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50">
                                <th class="text-left px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">No</th>
                                <th class="text-left px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Mata Pelajaran</th>
                                <th class="text-left px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden sm:table-cell">Guru Pengampu</th>
                                <th class="text-center px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden md:table-cell">Sudah Input</th>
                                <th class="text-center px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden md:table-cell">Belum Input</th>
                                <th class="text-left px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Progres</th>
                                <th class="text-center px-6 py-3 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <tr
                                v-for="(item, index) in daftarMonitoring"
                                :key="index"
                                class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150"
                            >
                                <td class="px-6 py-4 text-xs font-bold text-slate-400 dark:text-slate-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-800 dark:text-white">{{ item.mapel }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 sm:hidden mt-0.5">{{ item.guru }}</p>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ item.guru?.charAt(0)?.toUpperCase() || '?' }}
                                        </div>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[160px]">{{ item.guru }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ item.sudah_input }}</span>
                                    <span class="text-xs text-slate-400"> / {{ item.total_siswa }}</span>
                                </td>
                                <td class="px-6 py-4 text-center hidden md:table-cell">
                                    <span class="text-sm font-bold" :class="item.belum_input > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400'">
                                        {{ item.belum_input }}
                                    </span>
                                    <span class="text-xs text-slate-400"> siswa</span>
                                </td>
                                <td class="px-6 py-4 min-w-[140px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                            <div
                                                class="h-2 rounded-full transition-all duration-700 ease-out"
                                                :class="progressColor(item.persentase)"
                                                :style="{ width: item.persentase + '%' }"
                                            ></div>
                                        </div>
                                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 w-9 text-right shrink-0">{{ item.persentase }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold"
                                        :class="{
                                            'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400': item.status === 'Selesai',
                                            'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400': item.status === 'Sebagian',
                                            'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400': item.status === 'Belum',
                                        }"
                                    >
                                        <span v-if="item.status === 'Selesai'">✓</span>
                                        <span v-else-if="item.status === 'Sebagian'">◐</span>
                                        <span v-else>✕</span>
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

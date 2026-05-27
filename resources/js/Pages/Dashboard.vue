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

// Calculate percentages for donut chart (Jenis Kelamin)
const lakiCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'L');
    return item ? item.total : 0;
});

const perempuanCount = computed(() => {
    const item = props.chart_siswa_jk?.find(x => x.jenis_kelamin === 'P');
    return item ? item.total : 0;
});

const totalJK = computed(() => lakiCount.value + perempuanCount.value || 1); // prevent division by zero
const lakiPercent = computed(() => Math.round((lakiCount.value / totalJK.value) * 100));
const perempuanPercent = computed(() => Math.round((perempuanCount.value / totalJK.value) * 100));

// Find max value for bar chart (Siswa per Kelas)
const maxSiswaKelas = computed(() => {
    if (!props.chart_siswa_kelas || props.chart_siswa_kelas.length === 0) return 40;
    return Math.max(...props.chart_siswa_kelas.map(k => k.total)) || 40;
});

// Calculate max values for nilai charts (max is generally 100)
const maxNilai = 100;

// Calculate attendance percentages
const kehadiranSakit = computed(() => Number(props.chart_kehadiran?.sakit || 0));
const kehadiranIzin = computed(() => Number(props.chart_kehadiran?.izin || 0));
const kehadiranAlpa = computed(() => Number(props.chart_kehadiran?.alpa || 0));
const totalKehadiran = computed(() => kehadiranSakit.value + kehadiranIzin.value + kehadiranAlpa.value || 1);

const sakitPercent = computed(() => Math.round((kehadiranSakit.value / totalKehadiran.value) * 100));
const izinPercent = computed(() => Math.round((kehadiranIzin.value / totalKehadiran.value) * 100));
const alpaPercent = computed(() => Math.round((kehadiranAlpa.value / totalKehadiran.value) * 100));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                <div>
                    <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tight">Dashboard Overview</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Ringkasan Sistem E-Rapot</p>
                </div>
            </div>
        </template>

        <div class="py-12 animate-fade-in">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-brand-600 to-indigo-600 overflow-hidden shadow-xl shadow-brand-500/20 sm:rounded-3xl relative animate-slide-up">
                    <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                        <svg class="w-64 h-64 -mt-10 -mr-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                    </div>
                    <div class="p-10 text-white relative z-10">
                        <h3 class="text-3xl font-black mb-2 tracking-tight">Selamat Datang, {{ $page.props.auth.user.name }}! 👋</h3>
                        <p class="text-brand-100 text-lg max-w-2xl font-medium">
                            Anda masuk sebagai <span class="font-black uppercase px-2.5 py-1 bg-white/20 rounded-lg text-sm shadow-sm tracking-widest">{{ $page.props.auth.user.role }}</span>. 
                            Gunakan menu di sebelah kiri untuk mengelola data rapot.
                        </p>

                        <div v-if="$page.props.auth.user.role === 'guru'" class="flex flex-col sm:flex-row gap-4 mt-8 max-w-3xl">
                            <div v-if="$page.props.auth.user.mapel_diampu" class="bg-white/10 rounded-2xl p-5 border border-white/20 shadow-lg backdrop-blur-md flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-1.5 bg-white/20 rounded-lg">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                    <p class="text-brand-100 text-xs font-bold uppercase tracking-widest">Mata Pelajaran Diampu</p>
                                </div>
                                <p class="text-white font-black text-xl tracking-tight leading-tight">{{ $page.props.auth.user.mapel_diampu }}</p>
                            </div>
                            
                            <div v-if="$page.props.auth.user.kelas_diampu" class="bg-indigo-900/40 rounded-2xl p-5 border border-indigo-400/30 shadow-lg backdrop-blur-md flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="p-1.5 bg-indigo-500/40 rounded-lg">
                                        <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                                    </div>
                                    <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">Tugas Tambahan</p>
                                </div>
                                <div class="flex items-end justify-between">
                                    <p class="text-white font-black text-xl tracking-tight leading-tight">Wali Kelas {{ $page.props.auth.user.kelas_diampu }}</p>
                                    <p class="text-indigo-200 text-sm font-bold bg-indigo-900/50 px-2 py-1 rounded-lg">{{ stats.total_siswa_wali }} Anak Didik</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex items-start gap-4 animate-slide-up" style="animation-delay: 0.05s;">
                    <div class="p-2 bg-amber-100 rounded-xl text-amber-600 shrink-0 mt-0.5">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-amber-800 uppercase tracking-widest mb-1">Status Tahun Ajaran Aktif</h4>
                        <p class="text-sm font-semibold text-amber-700 leading-relaxed">
                            Saat ini Anda berada pada <strong class="font-black bg-amber-200/50 px-1.5 py-0.5 rounded">Tahun Ajaran {{ stats.tahun_aktif?.tahun ?? 'Belum diset' }} Semester {{ stats.tahun_aktif?.semester ?? '-' }}</strong>. Pastikan Anda memasukkan nilai pada semester yang aktif ini agar tidak terjadi kesalahan rekapitulasi data.
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-up" style="animation-delay: 0.2s;">
                    <!-- Stat 1 -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex items-center relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-blue-50 rounded-full blur-2xl group-hover:bg-blue-100 transition-colors"></div>
                        <div class="p-4 bg-blue-100 text-blue-600 rounded-2xl mr-6 relative z-10 shadow-inner border border-blue-200/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Kelas</p>
                            <p class="text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_kelas }}</p>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex items-center relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100 transition-colors"></div>
                        <div class="p-4 bg-emerald-100 text-emerald-600 rounded-2xl mr-6 relative z-10 shadow-inner border border-emerald-200/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Siswa</p>
                            <p class="text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_siswa }}</p>
                        </div>
                    </div>

                    <!-- Stat 3 -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex items-center relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-purple-50 rounded-full blur-2xl group-hover:bg-purple-100 transition-colors"></div>
                        <div class="p-4 bg-purple-100 text-purple-600 rounded-2xl mr-6 relative z-10 shadow-inner border border-purple-200/50">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Mata Pelajaran</p>
                            <p class="text-4xl font-black text-slate-800 tracking-tight">{{ stats.total_mapel }}</p>
                        </div>
                    </div>
                </div>

                <!-- Diagrams Section -->
                <div v-if="$page.props.auth.user.role === 'admin' && chart_siswa_jk" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-slide-up" style="animation-delay: 0.3s;">
                    
                    <!-- Diagram Jenis Kelamin (Donut) -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex flex-col items-center">
                        <div class="w-full mb-8">
                            <h3 class="text-lg font-black text-slate-800">Distribusi Siswa (Gender)</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Laki-laki vs Perempuan</p>
                        </div>
                        
                        <div class="relative w-48 h-48 mb-8 flex-shrink-0">
                            <!-- CSS Conic Gradient Donut -->
                            <div class="w-full h-full rounded-full" 
                                 :style="`background: conic-gradient(#4f46e5 0% ${lakiPercent}%, #ec4899 ${lakiPercent}% 100%);`">
                            </div>
                            <!-- Donut hole -->
                            <div class="absolute inset-4 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total</span>
                                <span class="text-2xl font-black text-slate-800">{{ stats.total_siswa }}</span>
                            </div>
                        </div>
                        
                        <div class="w-full grid grid-cols-2 gap-4 mt-auto">
                            <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 flex flex-col items-center">
                                <span class="flex items-center gap-1.5 text-xs font-black text-indigo-400 uppercase tracking-widest mb-1">
                                    <div class="w-2 h-2 rounded-full bg-indigo-600"></div> Laki-laki
                                </span>
                                <span class="text-xl font-black text-indigo-700">{{ lakiCount }}</span>
                                <span class="text-[10px] font-bold text-indigo-400 mt-1">{{ lakiPercent }}%</span>
                            </div>
                            <div class="bg-pink-50/50 border border-pink-100 rounded-2xl p-4 flex flex-col items-center">
                                <span class="flex items-center gap-1.5 text-xs font-black text-pink-400 uppercase tracking-widest mb-1">
                                    <div class="w-2 h-2 rounded-full bg-pink-500"></div> Perempuan
                                </span>
                                <span class="text-xl font-black text-pink-700">{{ perempuanCount }}</span>
                                <span class="text-[10px] font-bold text-pink-400 mt-1">{{ perempuanPercent }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Diagram Bar Kelas -->
                    <div class="lg:col-span-2 bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex flex-col">
                        <div class="w-full mb-8">
                            <h3 class="text-lg font-black text-slate-800">Distribusi Siswa per Kelas</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Jumlah peserta didik tiap rombel</p>
                        </div>
                        
                        <div class="flex-1 flex items-end gap-3 h-56 pt-4 pb-2">
                            <template v-if="chart_siswa_kelas && chart_siswa_kelas.length > 0">
                                <div v-for="(k, idx) in chart_siswa_kelas" :key="idx" class="relative flex flex-col items-center flex-1 h-full group">
                                    <!-- Tooltip hover -->
                                    <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap z-10 pointer-events-none transform translate-y-2 group-hover:translate-y-0">
                                        {{ k.nama }} : {{ k.total }} Siswa
                                    </div>
                                    
                                    <!-- Bar Container -->
                                    <div class="w-full h-full flex items-end justify-center px-1">
                                        <div class="w-full max-w-[3rem] bg-brand-100 rounded-t-xl overflow-hidden relative" :style="`height: ${(k.total / maxSiswaKelas) * 100}%`">
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-brand-600 to-brand-400 w-full h-full group-hover:brightness-110 transition-all"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-[10px] font-black text-slate-500 rotate-[-45deg] origin-top-left md:rotate-0 md:origin-center truncate max-w-full text-center">
                                        {{ k.nama }}
                                    </div>
                                </div>
                            </template>
                            <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold text-slate-400">
                                Belum ada data kelas / siswa.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ANALYTICS SECTION (GRAFIK PRESTASI & KEHADIRAN) -->
                <div v-if="$page.props.auth.user.role === 'admin' || ($page.props.auth.user.role === 'guru' && $page.props.auth.user.kelas_diampu)" class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-slide-up" style="animation-delay: 0.4s;">
                    
                    <!-- Grafik Prestasi: Rata-rata Nilai per Kelas -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex flex-col">
                        <div class="w-full mb-8 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-black text-slate-800">Rata-rata Nilai per Kelas</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Akumulasi prestasi siswa</p>
                            </div>
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                        
                        <div class="flex-1 flex items-end gap-3 h-56 pt-4 pb-2">
                            <template v-if="chart_nilai_kelas && chart_nilai_kelas.length > 0">
                                <div v-for="(k, idx) in chart_nilai_kelas" :key="idx" class="relative flex flex-col items-center flex-1 h-full group">
                                    <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap z-10 pointer-events-none transform translate-y-2 group-hover:translate-y-0">
                                        {{ k.nama_kelas }} : {{ Number(k.rata_rata).toFixed(1) }}
                                    </div>
                                    <div class="w-full h-full flex items-end justify-center px-1">
                                        <div class="w-full max-w-[3rem] bg-emerald-100 rounded-t-xl overflow-hidden relative" :style="`height: ${(Number(k.rata_rata) / maxNilai) * 100}%`">
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-emerald-600 to-emerald-400 w-full h-full group-hover:brightness-110 transition-all"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-[10px] font-black text-slate-500 rotate-[-45deg] origin-top-left md:rotate-0 md:origin-center truncate max-w-full text-center">
                                        {{ k.nama_kelas }}
                                    </div>
                                </div>
                            </template>
                            <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold text-slate-400">
                                Belum ada data nilai.
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Prestasi: Rata-rata Nilai per Mapel -->
                    <div class="bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8 flex flex-col">
                        <div class="w-full mb-6 flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-black text-slate-800">Rata-rata per Mata Pelajaran</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Capaian akademik mapel</p>
                            </div>
                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <template v-if="chart_nilai_mapel && chart_nilai_mapel.length > 0">
                                <!-- Scrollable container -->
                                <div class="overflow-x-auto pb-2">
                                    <div class="flex items-end gap-2 h-52 pt-4"
                                         :style="`min-width: ${chart_nilai_mapel.length * 64}px`">
                                        <div
                                            v-for="(m, idx) in chart_nilai_mapel"
                                            :key="idx"
                                            class="relative flex flex-col items-center group"
                                            style="width: 56px; flex-shrink: 0; height: 100%;"
                                        >
                                            <!-- Tooltip -->
                                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-800 text-white text-[10px] font-bold px-2 py-1 rounded-lg whitespace-nowrap z-20 pointer-events-none">
                                                {{ m.nama_mapel }}: {{ Number(m.rata_rata).toFixed(1) }}
                                            </div>

                                            <!-- Nilai label atas bar -->
                                            <div class="text-[9px] font-black text-indigo-600 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ Number(m.rata_rata).toFixed(0) }}
                                            </div>

                                            <!-- Bar wrapper: fill remaining height -->
                                            <div class="w-full flex-1 flex items-end justify-center">
                                                <div
                                                    class="w-9 bg-indigo-100 rounded-t-lg overflow-hidden relative transition-all duration-300"
                                                    :style="`height: ${(Number(m.rata_rata) / maxNilai) * 100}%`"
                                                >
                                                    <div class="absolute inset-0 bg-gradient-to-t from-indigo-600 to-indigo-400 group-hover:brightness-110 transition-all"></div>
                                                </div>
                                            </div>

                                            <!-- Label bawah - terpotong rapi -->
                                            <div class="mt-1 text-[9px] font-bold text-slate-500 text-center leading-tight w-full px-0.5" style="word-break: break-word; hyphens: auto;">
                                                {{ m.nama_mapel.length > 10 ? m.nama_mapel.substring(0, 9) + '.' : m.nama_mapel }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="w-full h-52 flex items-center justify-center text-sm font-bold text-slate-400">
                                Belum ada data mapel.
                            </div>
                        </div>
                    </div>

                    <!-- Statistik Kehadiran (Horizontal Bar) -->
                    <div class="lg:col-span-2 bg-white rounded-3xl shadow-[0_4px_24px_rgba(0,0,0,0.02)] border border-slate-100 p-8">
                        <div class="w-full mb-8">
                            <h3 class="text-lg font-black text-slate-800">Statistik Ketidakhadiran Global</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Akumulasi Sakit, Izin, Alpa se-sekolah</p>
                        </div>
                        
                        <div v-if="totalKehadiran > 1" class="space-y-6">
                            <!-- Sakit -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-bold text-slate-600 flex items-center gap-2"><div class="w-3 h-3 rounded bg-amber-400"></div> Sakit</span>
                                    <span class="text-sm font-black text-amber-600">{{ kehadiranSakit }} Hari ({{ sakitPercent }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-4 rounded-full transition-all duration-1000" :style="`width: ${sakitPercent}%`"></div>
                                </div>
                            </div>
                            <!-- Izin -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-bold text-slate-600 flex items-center gap-2"><div class="w-3 h-3 rounded bg-blue-400"></div> Izin</span>
                                    <span class="text-sm font-black text-blue-600">{{ kehadiranIzin }} Hari ({{ izinPercent }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 h-4 rounded-full transition-all duration-1000" :style="`width: ${izinPercent}%`"></div>
                                </div>
                            </div>
                            <!-- Alpa -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-sm font-bold text-slate-600 flex items-center gap-2"><div class="w-3 h-3 rounded bg-rose-500"></div> Tanpa Keterangan (Alpa)</span>
                                    <span class="text-sm font-black text-rose-600">{{ kehadiranAlpa }} Hari ({{ alpaPercent }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-4 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-rose-500 to-rose-600 h-4 rounded-full transition-all duration-1000" :style="`width: ${alpaPercent}%`"></div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="w-full h-32 flex items-center justify-center text-sm font-bold text-slate-400 bg-slate-50 rounded-2xl">
                            Belum ada riwayat presensi yang dimasukkan oleh wali kelas.
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeInAnim 0.15s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
@keyframes fadeInAnim { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
</style>

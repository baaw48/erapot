<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    semuaTahun: Array,
    kelas: Array,
});

const selectedTahunId = ref('');

const tahunTerpilih = computed(() => {
    if (!selectedTahunId.value) return null;
    return props.semuaTahun.find(t => t.id == selectedTahunId.value);
});

const labelSemester = (sem) => {
    if (sem === 'Ganjil') return '📘 Ganjil';
    if (sem === 'Genap') return '📗 Genap';
    return sem;
};

const urlRapor = (kelasId) => {
    return route('arsip.pdf', { kelas_id: kelasId, tahun_ajaran_id: selectedTahunId.value });
};

const urlLeger = (kelasId) => {
    return route('arsip-leger.pdf', { kelas_id: kelasId, tahun_ajaran_id: selectedTahunId.value });
};
</script>

<template>
    <Head title="Arsip Rapor" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl dark:text-white leading-tight tracking-tight">Arsip Rapor</h2>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Cetak Rapor & Leger Tahun Lalu</p>
                </div>
            </div>
        </template>

        <div class="animate-fade-in space-y-6 pb-12">

            <!-- Info Banner -->
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex items-start gap-4 animate-slide-up">
                <div class="p-2 bg-amber-100 rounded-xl text-amber-600 shrink-0 mt-0.5">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-amber-800 uppercase tracking-widest mb-1">Mode Hanya Baca (Read-Only)</h4>
                    <p class="text-sm font-semibold dark:text-amber-300 leading-relaxed">
                        Halaman ini digunakan untuk <strong>melihat dan mencetak</strong> rapor dari tahun-tahun ajaran yang sudah tersimpan. Untuk mengedit data nilai tahun lalu, gunakan fitur <strong>Ganti Tahun Ajaran Aktif</strong> di menu Pengaturan.
                    </p>
                </div>
            </div>

            <!-- Step 1: Pilih Tahun Ajaran -->
            <div class="bg-white border border-slate-200 dark:border-slate-700 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="px-8 py-6 border-b border-slate-100 dark:bg-slate-800/50 backdrop-blur-sm relative z-10">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="h-7 w-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-black text-sm shrink-0">1</div>
                        <h3 class="text-base font-black dark:text-white">Pilih Tahun Ajaran</h3>
                    </div>
                    <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 ml-10">Pilih periode tahun ajaran yang ingin Anda lihat arsipnya.</p>
                </div>
                <div class="p-8 relative z-10">
                    <div v-if="semuaTahun.length === 0" class="text-center py-8 text-slate-400 dark:text-slate-500 font-semibold text-sm">
                        Belum ada data tahun ajaran yang tersimpan.
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <button
                            v-for="tahun in semuaTahun"
                            :key="tahun.id"
                            @click="selectedTahunId = tahun.id"
                            :class="[
                                'relative rounded-2xl p-5 text-left border-2 transition-all duration-200 group',
                                selectedTahunId == tahun.id
                                    ? 'border-amber-400 bg-amber-50 shadow-lg shadow-amber-500/10'
                                    : 'border-slate-100 bg-slate-50 dark:bg-slate-800/50 hover:border-amber-200 hover:bg-amber-50/50 hover:-translate-y-0.5'
                            ]"
                        >
                            <div v-if="tahun.is_active" class="absolute top-3 right-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-green-700 dark:text-green-300 text-[9px] font-black uppercase tracking-widest rounded-full border border-green-200 dark:border-green-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-50 dark:bg-green-900/200 animate-pulse"></span>
                                    Aktif
                                </span>
                            </div>
                            <div :class="['h-10 w-10 rounded-xl flex items-center justify-center font-black text-sm mb-3 transition-colors', selectedTahunId == tahun.id ? 'bg-amber-500 dark:bg-amber-600 text-white' : 'bg-white text-slate-600 dark:text-slate-300 border border-slate-200 group-hover:bg-amber-100 group-hover:dark:text-amber-300']">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p :class="['font-black text-base', selectedTahunId == tahun.id ? 'dark:text-amber-300' : 'dark:text-white']">{{ tahun.tahun }}</p>
                            <p :class="['font-bold text-xs mt-0.5', selectedTahunId == tahun.id ? 'text-amber-500' : 'text-slate-400 dark:text-slate-500']">Semester {{ tahun.semester }}</p>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Pilih Kelas & Cetak (muncul setelah tahun dipilih) -->
            <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0">
                <div v-if="selectedTahunId" class="bg-white border border-slate-200 dark:border-slate-700 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative">
                    <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-green-50 dark:bg-green-900/20 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                    <div class="px-8 py-6 border-b border-slate-100 dark:bg-slate-800/50 backdrop-blur-sm relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <div class="h-7 w-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-sm shrink-0">2</div>
                                <h3 class="text-base font-black dark:text-white">Pilih Kelas & Cetak Dokumen</h3>
                            </div>
                            <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 ml-10">Klik tombol cetak di baris kelas yang ingin Anda unduh.</p>
                        </div>
                        <div class="shrink-0 px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-2xl text-right">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Melihat Arsip Periode</p>
                            <p class="text-sm font-black text-amber-800 mt-0.5">TA {{ tahunTerpilih?.tahun }} &mdash; Sem. {{ tahunTerpilih?.semester }}</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto relative z-10">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                    <th class="px-6 py-4 w-16 text-center">No.</th>
                                    <th class="px-8 py-4 w-1/3">Nama Kelas</th>
                                    <th class="px-8 py-4 w-1/3">Wali Kelas</th>
                                    <th class="px-8 py-4 text-center">Cetak Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="(k, index) in kelas" :key="k.id" class="hover:bg-slate-50 dark:bg-slate-800/50/50 transition-colors group">
                                    <td class="px-6 py-5 text-center text-sm font-bold text-slate-400 dark:text-slate-500">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-black shadow-inner">
                                                {{ k.tingkat }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold dark:text-white">{{ k.nama_kelas }}</div>
                                                <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5">Tingkat {{ k.tingkat }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 dark:text-slate-400 dark:text-slate-500 flex items-center justify-center text-[10px] font-black uppercase shrink-0">
                                                {{ k.wali_kelas ? k.wali_kelas.name.charAt(0) : '?' }}
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-white dark:text-white dark:text-slate-200">{{ k.wali_kelas ? k.wali_kelas.name : 'Belum Diatur' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <a
                                                :href="urlLeger(k.id)"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:bg-emerald-600 hover:text-white px-4 py-2.5 text-xs font-bold tracking-widest transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 border border-green-200 dark:border-green-800 hover:border-emerald-600"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Leger
                                            </a>
                                            <a
                                                :href="urlRapor(k.id)"
                                                target="_blank"
                                                class="inline-flex items-center gap-2 rounded-xl bg-amber-50 dark:text-amber-300 hover:bg-amber-500 dark:bg-amber-600 hover:text-white px-4 py-2.5 text-xs font-bold tracking-widest transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5 border border-amber-200 hover:border-amber-500"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Rapor
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="kelas.length === 0">
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 text-slate-300 mb-4">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        </div>
                                        <h3 class="text-sm font-black text-slate-600 dark:text-slate-300">Belum Ada Data Kelas</h3>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </Transition>

            <!-- Placeholder ketika belum pilih tahun -->
            <div v-if="!selectedTahunId" class="bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 rounded-3xl p-16 text-center animate-slide-up" style="animation-delay: 0.1s; animation-fill-mode: both;">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-amber-50 text-amber-300 mb-4">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-base font-black text-slate-500 dark:text-slate-400 dark:text-slate-500">Belum Ada Tahun Dipilih</h3>
                <p class="text-sm font-semibold text-slate-400 dark:text-slate-500 mt-1">Pilih periode tahun ajaran di atas untuk menampilkan daftar kelas yang bisa dicetak.</p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeInAnim 0.15s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
@keyframes fadeInAnim { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { 0% { opacity: 0; transform: translateY(24px); } 100% { opacity: 1; transform: translateY(0); } }
</style>

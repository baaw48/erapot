<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    tahunAktif: Object,
    kelas: Array,
});
</script>

<template>
    <Head title="Cetak Dokumen Rapor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl dark:text-white leading-tight tracking-tight">Cetak Dokumen Rapor</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Cetak Leger & Rapor Siswa</p>
                </div>
            </div>
        </template>

        <div class="animate-fade-in space-y-6">
            <div v-if="!tahunAktif" class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="font-bold text-sm">Belum ada Tahun Ajaran yang aktif. Silakan atur di menu Pengaturan.</span>
            </div>

            <div v-else class="bg-white border border-slate-200 dark:border-slate-700 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <!-- Dekorasi -->
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-100 dark:bg-blue-900/30 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-green-50 dark:bg-green-900/20 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="px-8 py-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-black dark:text-white">Daftar Kelas</h3>
                            <p class="text-sm font-semibold text-slate-500 mt-1">Pilih kelas yang ingin dicetak rapor atau leger-nya.</p>
                        </div>
                        <div class="px-4 py-2 bg-blue-100 dark:bg-blue-900/30 border border-brand-100 rounded-xl inline-flex flex-col">
                            <span class="text-[10px] font-black text-brand-500 uppercase tracking-widest">Tahun Ajaran Aktif</span>
                            <span class="text-sm font-bold text-brand-700">{{ tahunAktif.tahun }} ({{ tahunAktif.semester }})</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4 w-16 text-center">No.</th>
                                    <th class="px-8 py-4 w-1/3">Nama Kelas</th>
                                    <th class="px-8 py-4 w-1/3">Wali Kelas</th>
                                    <th class="px-8 py-4 text-center">Tindakan Pencetakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="(k, index) in kelas" :key="k.id" class="hover:bg-slate-50 dark:bg-slate-800/50/50 transition-colors group">
                                    <td class="px-6 py-5 text-center text-sm font-bold text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-xl bg-brand-100 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black shadow-inner">
                                                {{ k.tingkat }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold dark:text-white">{{ k.nama_kelas }}</div>
                                                <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Tingkat {{ k.tingkat }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black uppercase shrink-0">
                                                {{ k.wali_kelas ? k.wali_kelas.name.charAt(0) : '?' }}
                                            </div>
                                            <span class="text-sm font-bold dark:text-slate-200">{{ k.wali_kelas ? k.wali_kelas.name : 'Belum Diatur' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <a :href="route('cetak-leger.pdf', { kelas_id: k.id })" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:bg-emerald-600 hover:text-white px-5 py-2.5 text-xs font-bold tracking-widest transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/30 hover:-translate-y-0.5 border border-green-200 dark:border-green-800 hover:border-emerald-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Cetak Leger
                                            </a>
                                            <a :href="route('cetak.pdf', { kelas_id: k.id })" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-brand-700 hover:bg-brand-600 hover:text-white px-5 py-2.5 text-xs font-bold tracking-widest transition-all duration-300 hover:shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5 border border-brand-200 hover:border-brand-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                Cetak Rapor
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="kelas.length === 0">
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 text-slate-300 mb-4">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h3 class="text-sm font-black text-slate-600">Belum Ada Kelas</h3>
                                        <p class="text-xs font-medium text-slate-400 mt-1">Data kelas tidak ditemukan untuk dicetak.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeInAnim 0.15s ease-out forwards;
}
.animate-slide-up {
    animation: slideUp 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes fadeInAnim {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    0% { opacity: 0; transform: translateY(24px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

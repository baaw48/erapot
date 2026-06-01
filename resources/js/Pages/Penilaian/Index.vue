<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    tahunAktif: Object,
    kelas: Array,
    mapels: Array,
    siswas: Array,
    mapelSudahInput: {
        type: Array,
        default: () => []
    },
    filters: Object,
});

const form = useForm({
    kelas_id: props.filters?.kelas_id || '',
    mapel_id: props.filters?.mapel_id || '',
    nilais: []
});

const showSuccessModal = ref(false);

watch(() => props.siswas, (newSiswas) => {
    if (newSiswas && newSiswas.length > 0) {
        form.nilais = newSiswas.map(siswa => ({
            siswa_id: siswa.siswa_id,
            nilai_angka: siswa.nilai_angka !== null ? siswa.nilai_angka : ''
        }));
    } else {
        form.nilais = [];
    }
}, { immediate: true });

watch(() => [form.kelas_id, form.mapel_id], ([newKelasId, newMapelId]) => {
    if (newKelasId || newMapelId) {
        router.reload({
            only: ['siswas', 'filters', 'mapelSudahInput'],
            data: { kelas_id: newKelasId || '', mapel_id: newMapelId || '' },
            preserveState: true,
            preserveScroll: true,
        });
    }
});

const page = usePage();

const submit = () => {
    form.post(route('nilai.storeBatch'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
        },
    });
};

const bulkNilai = ref('');
const applyBulkNilai = () => {
    if (bulkNilai.value !== '' && bulkNilai.value !== null) {
        form.nilais.forEach(n => {
            n.nilai_angka = bulkNilai.value;
        });
    }
};

const getGradeClass = (val) => {
    if (val === '' || val === null) return 'border-slate-200 bg-slate-50 dark:text-white focus:border-brand-500 focus:ring-brand-500/20';
    const num = Number(val);
    if (num >= 85) return 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.1)]';
    if (num >= 70) return 'border-amber-200 bg-amber-50 text-amber-700 focus:border-amber-500 focus:ring-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.1)]';
    return 'border-rose-200 bg-rose-50 text-rose-700 focus:border-rose-500 focus:ring-rose-500/20 shadow-[0_0_10px_rgba(225,29,72,0.1)]';
};

const getGradeBadge = (val) => {
    if (val === '' || val === null) return { text: '-', class: 'bg-slate-100 dark:text-slate-400 dark:text-slate-500' };
    const num = Number(val);
    if (num >= 85) return { text: 'Sangat Baik', class: 'bg-emerald-100 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' };
    if (num >= 70) return { text: 'Cukup', class: 'bg-amber-100 text-amber-700 border border-amber-200' };
    return { text: 'Kurang', class: 'bg-rose-100 text-rose-700 border border-rose-200' };
};

const kelasOptions = computed(() => [
    { value: '', label: '-- Klik untuk memilih kelas --' },
    ...(props.kelas || []).map(k => ({ value: k.id, label: k.nama_kelas })),
]);

const mapelOptions = computed(() => [
    { value: '', label: '-- Klik untuk memilih mapel --' },
    ...(props.mapels || []).map(m => ({
        value: m.id,
        label: m.nama_mapel + (props.mapelSudahInput.includes(m.id) ? ' ✅ (Sudah)' : ''),
    })),
]);
</script>

<template>
    <Head :title="$page.props.auth.user.role === 'admin' ? 'Monitoring Nilai' : `Input Nilai ${$page.props.sekolah?.jenis_asesmen || 'ASTS'}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl dark:text-white leading-tight tracking-tight">
                        {{ $page.props.auth.user.role === 'admin' ? 'Monitoring Nilai' : `Input Nilai ${$page.props.sekolah?.jenis_asesmen || 'ASTS'}` }}
                    </h2>
                    <p class="text-xs font-bold dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Kelola & pantau nilai siswa</p>
                </div>
            </div>
        </template>

        <div class="animate-fade-in space-y-6">
            
            <!-- Notifikasi Flash -->
            <div v-if="page.props.flash && page.props.flash.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.success }}</span>
            </div>

            <div v-if="page.props.flash && page.props.flash.error" class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.error }}</span>
            </div>

            <!-- Panel Filter -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative overflow-hidden">
                <!-- Dekorasi Background -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-100 dark:bg-blue-900/30 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Pilih Kelas
                        </label>
                        <SearchableSelect
                            v-model="form.kelas_id"
                            :options="kelasOptions"
                            placeholder="-- Klik untuk memilih kelas --"
                            searchPlaceholder="Ketik nama kelas..."
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Pilih Mata Pelajaran
                        </label>
                        <SearchableSelect
                            v-model="form.mapel_id"
                            :options="mapelOptions"
                            placeholder="-- Klik untuk memilih mapel --"
                            searchPlaceholder="Ketik nama mapel..."
                            :disabled="!form.kelas_id"
                        />
                    </div>
                </div>
            </div>

            <!-- Area Data & Form -->
            <div v-if="siswas && siswas.length > 0" class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <form @submit.prevent="submit">
                    <!-- Header Tabel -->
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-brand-100 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-sm shrink-0">
                                {{ siswas.length }}
                            </div>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Siswa Terdaftar</span>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <!-- Fitur Setel Semua Nilai -->
                            <div class="flex-1 md:flex-none flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                                <span class="text-[10px] font-black dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest hidden sm:inline">Set Semua Nilai:</span>
                                <input type="number" min="0" max="100" v-model="bulkNilai" @input="applyBulkNilai" placeholder="Ketik angka..." class="w-full sm:w-28 h-8 text-center text-sm font-black border-none bg-slate-50 rounded-lg focus:ring-0" title="Ketik angka di sini, maka semua nilai siswa di bawah akan otomatis terisi">
                            </div>
                            
                            <button type="button" @click="submit" class="hidden md:flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                <span class="whitespace-nowrap">Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4 w-32">NIS</th>
                                    <th class="px-6 py-4">Nama Lengkap</th>
                                    <th class="px-6 py-4 w-40 text-center">Predikat</th>
                                    <th class="px-6 py-4 w-40 text-center">Nilai Angka</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="(siswa, index) in siswas" :key="siswa.siswa_id" class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-bold dark:text-slate-400 dark:text-slate-500 group-hover:text-brand-500 transition-colors">{{ index + 1 }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 font-mono">{{ siswa.nis }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 dark:text-slate-400 dark:text-slate-500 flex items-center justify-center text-xs font-black uppercase shrink-0">
                                                {{ siswa.nama_siswa.charAt(0) }}
                                            </div>
                                            <span class="text-sm font-bold dark:text-white">{{ siswa.nama_siswa }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold" :class="getGradeBadge(form.nilais[index].nilai_angka).class">
                                            {{ getGradeBadge(form.nilais[index].nilai_angka).text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 flex justify-center">
                                        <div class="relative">
                                            <input 
                                                type="number" 
                                                min="0" 
                                                max="100" 
                                                v-model="form.nilais[index].nilai_angka"
                                                class="w-20 text-center font-black text-lg rounded-xl transition-all duration-300 placeholder:text-slate-300 placeholder:font-normal placeholder:text-sm"
                                                :class="getGradeClass(form.nilais[index].nilai_angka)"
                                                placeholder="0"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer / Floating Action Mobile -->
                    <div class="px-6 py-5 border-t border-slate-100 bg-slate-50 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Semua Nilai' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Empty State -->
            <div v-else-if="form.kelas_id && form.mapel_id" class="bg-white border border-slate-100 rounded-3xl p-12 text-center shadow-[0_4px_24px_rgba(0,0,0,0.02)] animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 text-slate-300 mb-4">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-700 dark:text-white dark:text-white dark:text-slate-200 mb-1">Belum Ada Siswa</h3>
                <p class="text-sm font-medium dark:text-slate-400 dark:text-slate-500">Tidak ada data siswa yang terdaftar di kelas ini.</p>
            </div>
            
            <div v-else class="flex flex-col items-center justify-center py-16 opacity-50">
                <svg class="h-16 w-16 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <p class="text-sm font-bold dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">Silakan pilih Kelas & Mapel terlebih dahulu</p>
            </div>
        </div>

        <!-- Custom Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-50 dark:bg-green-900/200 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-50 dark:bg-green-900/20 mb-6 shadow-inner border border-emerald-100 relative z-10">
                    <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black dark:text-white mb-2 relative z-10">
                    Berhasil Disimpan!
                </h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 mb-8 relative z-10">
                    {{ page.props.flash?.success || 'Seluruh nilai mata pelajaran untuk kelas ini telah berhasil disimpan ke database.' }}
                </p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup Jendela
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Animations that match the Tailwind config injected in RAPOTAPPSCRIPT */
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

/* Hilangkan panah spinner di input number (Chrome/Safari/Edge) */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>

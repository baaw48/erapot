<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    kelas: Array,
    siswas: Array,
    filters: Object,
});

const tipeMode = ref(props.filters.tipe || 'kenaikan');
const kelasAsalId = ref(props.filters.kelas_asal_id || '');
const kelasTujuanId = ref('');

const kelasOptions = computed(() => [
    { value: '', label: '-- Pilih Kelas --' },
    ...(props.kelas || []).map(k => ({ value: k.id, label: k.nama_kelas })),
]);

// Untuk checkbox siswa
const selectedSiswas = ref([]);
const selectAll = ref(false);

watch(kelasAsalId, (newId) => {
    router.get(route('kenaikan.index'), { kelas_asal_id: newId, tipe: tipeMode.value }, { preserveState: true });
    selectedSiswas.value = [];
    selectAll.value = false;
});

watch(tipeMode, (newTipe) => {
    router.get(route('kenaikan.index'), { kelas_asal_id: kelasAsalId.value, tipe: newTipe }, { preserveState: true });
});

// Jika data siswas berubah (baru di-load), default check all
watch(() => props.siswas, (newSiswas) => {
    if (newSiswas && newSiswas.length > 0) {
        selectedSiswas.value = newSiswas.map(s => s.id);
        selectAll.value = true;
    } else {
        selectedSiswas.value = [];
        selectAll.value = false;
    }
}, { immediate: true });

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedSiswas.value = props.siswas.map(s => s.id);
    } else {
        selectedSiswas.value = [];
    }
};

// Form submission
const showConfirmModal = ref(false);
const showSuccessModal = ref(false);

const form = useForm({
    kelas_asal_id: '',
    kelas_tujuan_id: '',
    siswa_ids: []
});

const confirmProses = () => {
    if (selectedSiswas.value.length === 0) {
        alert('Pilih minimal 1 siswa!');
        return;
    }
    
    if (tipeMode.value === 'kenaikan' && !kelasTujuanId.value) {
        alert('Pilih Kelas Tujuan terlebih dahulu!');
        return;
    }

    form.kelas_asal_id = kelasAsalId.value;
    form.kelas_tujuan_id = kelasTujuanId.value;
    form.siswa_ids = selectedSiswas.value;
    
    showConfirmModal.value = true;
};

const executeProses = () => {
    showConfirmModal.value = false;
    if (tipeMode.value === 'kenaikan') {
        form.post(route('kenaikan.promote'), {
            onSuccess: () => {
                showSuccessModal.value = true;
                kelasAsalId.value = '';
                kelasTujuanId.value = '';
            }
        });
    } else {
        form.post(route('kenaikan.graduate'), {
            onSuccess: () => {
                showSuccessModal.value = true;
                kelasAsalId.value = '';
            }
        });
    }
};

</script>

<template>
    <Head title="Kenaikan & Kelulusan" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Kenaikan & Kelulusan" 
            description="Kelola proses pemindahan dan kelulusan peserta didik."
            icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
        />

        <div class="animate-fade-in space-y-6 max-w-7xl mx-auto pb-12">
            
            <div v-if="$page.props.flash && $page.props.flash.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Panel Mode Selector -->
            <div class="flex gap-4 p-2 bg-slate-100 rounded-2xl max-w-md mx-auto relative z-10">
                <button @click="tipeMode = 'kenaikan'" :class="tipeMode === 'kenaikan' ? 'bg-white shadow-sm text-blue-600 dark:text-blue-400' : 'text-slate-500 dark:text-slate-400 dark:text-slate-500 hover:dark:text-slate-200'" class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all">
                    Kenaikan Kelas
                </button>
                <button @click="tipeMode = 'kelulusan'" :class="tipeMode === 'kelulusan' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-500 dark:text-slate-400 dark:text-slate-500 hover:dark:text-slate-200'" class="flex-1 py-2.5 text-sm font-bold rounded-xl transition-all">
                    Kelulusan (Alumni)
                </button>
            </div>

            <!-- Konten Utama -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-[2rem] overflow-hidden shadow-2xl relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-blue-100 dark:bg-blue-900/30 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/30 backdrop-blur-sm relative z-10">
                    <!-- Info Alert -->
                    <div class="mb-8 p-5 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4 items-start">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-xl shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-blue-800 text-sm mb-1">{{ tipeMode === 'kenaikan' ? 'Petunjuk Kenaikan Kelas' : 'Petunjuk Kelulusan' }}</h4>
                            <p class="text-xs font-semibold text-blue-600/80 leading-relaxed">
                                {{ tipeMode === 'kenaikan' 
                                    ? 'Pilih Kelas Asal untuk melihat daftar siswa. Hapus centang pada siswa yang tidak naik kelas (tinggal kelas), lalu pilih Kelas Tujuan.' 
                                    : 'Pilih Kelas Akhir (misal Kelas 9) untuk meluluskan siswa. Siswa yang diluluskan akan dihapus dari daftar siswa aktif dan masuk ke arsip Alumni.' 
                                }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                        <!-- Kelas Asal -->
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-black text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <span class="w-5 h-5 rounded-md bg-slate-100 flex items-center justify-center text-slate-600 dark:text-slate-300">1</span>
                                Pilih Kelas Asal
                            </label>
                            <SearchableSelect
                                v-model="kelasAsalId"
                                :options="kelasOptions"
                                placeholder="-- Pilih Kelas Asal --"
                                searchPlaceholder="Ketik nama kelas..."
                            />
                        </div>

                        <!-- Kelas Tujuan (Hanya Kenaikan) -->
                        <div v-if="tipeMode === 'kenaikan'" class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-black text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <span class="w-5 h-5 rounded-md bg-slate-100 flex items-center justify-center text-slate-600 dark:text-slate-300">2</span>
                                Pilih Kelas Tujuan
                            </label>
                            <SearchableSelect
                                v-model="kelasTujuanId"
                                :options="kelasOptions.filter(o => o.value !== kelasAsalId)"
                                placeholder="-- Pilih Kelas Tujuan --"
                                searchPlaceholder="Ketik nama kelas..."
                                :disabled="!kelasAsalId"
                            />
                        </div>
                        
                        <!-- Tombol Kelulusan -->
                        <div v-if="tipeMode === 'kelulusan'" class="flex items-center h-[52px]">
                            <button @click="confirmProses" :disabled="!kelasAsalId || selectedSiswas.length === 0" class="w-full h-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:hover:translate-y-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                Luluskan {{ selectedSiswas.length }} Siswa
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Siswa -->
                <div v-if="kelasAsalId" class="relative z-10">
                    <div class="bg-slate-50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-700/50 px-8 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="selectAll" v-model="selectAll" @change="toggleSelectAll" class="w-5 h-5 rounded-md border-slate-300 text-blue-600 dark:text-blue-400 focus:ring-brand-500 cursor-pointer">
                            <label for="selectAll" class="text-xs font-black text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest cursor-pointer select-none">Pilih Semua Siswa</label>
                        </div>
                        <div class="text-xs font-black text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                            Terpilih: <span class="text-blue-600 dark:text-blue-400 text-sm">{{ selectedSiswas.length }}</span> / {{ siswas.length }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                <tr v-for="(siswa, index) in siswas" :key="siswa.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group">
                                    <td class="px-8 py-4 w-12">
                                        <input type="checkbox" :id="'siswa_'+siswa.id" :value="siswa.id" v-model="selectedSiswas" class="w-5 h-5 rounded-md border-slate-300 text-blue-600 dark:text-blue-400 focus:ring-brand-500 cursor-pointer">
                                    </td>
                                    <td class="px-2 py-4 w-12 text-center text-sm font-bold text-slate-400 dark:text-slate-500">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <label :for="'siswa_'+siswa.id" class="flex items-center gap-4 cursor-pointer">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center font-black text-sm shrink-0"
                                                :class="siswa.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-600' : 'bg-pink-100 text-pink-600'">
                                                {{ siswa.nama_siswa.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold dark:text-white">{{ siswa.nama_siswa }}</div>
                                                <div class="text-[11px] font-bold text-slate-400 dark:text-slate-500 mt-0.5">NIS: {{ siswa.nis }}</div>
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                                <tr v-if="siswas.length === 0">
                                    <td colspan="3" class="px-8 py-16 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 text-slate-300 mb-4">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        </div>
                                        <h3 class="text-sm font-black text-slate-600 dark:text-slate-300">Siswa Tidak Ditemukan</h3>
                                        <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-1">Belum ada siswa aktif di kelas ini.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div v-if="tipeMode === 'kenaikan' && siswas.length > 0" class="p-6 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 flex justify-end">
                        <button @click="confirmProses" :disabled="!kelasTujuanId || selectedSiswas.length === 0" class="w-full inline-flex items-center justify-center gap-2 px-5 py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:hover:translate-y-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                            Proses Pindah Ke Kelas Tujuan
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Confirm Modal -->
        <Modal :show="showConfirmModal" @close="showConfirmModal = false" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full blur-3xl opacity-20 pointer-events-none" :class="tipeMode === 'kenaikan' ? 'bg-blue-100 dark:bg-blue-900/300' : 'bg-indigo-500'"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6 relative z-10" :class="tipeMode === 'kenaikan' ? 'bg-blue-100 dark:bg-blue-900/30 text-brand-500' : 'bg-indigo-50 text-indigo-500'">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-xl font-black dark:text-white mb-2 relative z-10">Konfirmasi Proses</h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 mb-8 relative z-10">
                    Apakah Anda yakin ingin <strong :class="tipeMode === 'kenaikan' ? 'text-blue-600 dark:text-blue-400' : 'text-indigo-600'">{{ tipeMode === 'kenaikan' ? 'Menaikkan' : 'Meluluskan' }}</strong> {{ selectedSiswas.length }} siswa yang dipilih?
                </p>
                <div class="flex gap-3 relative z-10">
                    <button @click="showConfirmModal = false" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-800/50 dark:text-slate-200 text-sm font-bold rounded-xl transition-all">
                        Batal
                    </button>
                    <button @click="executeProses" class="flex-1 px-4 py-3 text-white text-sm font-bold rounded-xl shadow-md transition-all" :class="tipeMode === 'kenaikan' ? 'bg-brand-600 hover:bg-brand-700' : 'bg-indigo-600 hover:bg-indigo-700'">
                        Ya, Proses
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-50 dark:bg-green-900/200 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-50 dark:bg-green-900/20 mb-6 shadow-inner border border-emerald-100 relative z-10">
                    <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-black dark:text-white mb-2 relative z-10">Berhasil!</h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 mb-8 relative z-10">
                    Proses {{ tipeMode }} telah berhasil dilakukan untuk {{ form.siswa_ids.length }} siswa.
                </p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeInAnim 0.15s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
@keyframes fadeInAnim { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { 0% { opacity: 0; transform: translateY(24px); } 100% { opacity: 1; transform: translateY(0); } }
</style>

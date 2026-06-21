<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    pembelajarans: Object, // Dictionary keyed by mapel_id
    gurus: Array,
    kelas: Array,
    mapels: Array,
    tahunAktif: Object,
    selectedKelasId: [Number, String]
});

const filterKelasId = ref(props.selectedKelasId || '');

const selectedKelas = computed(() => {
    return props.kelas ? props.kelas.find(k => k.id == filterKelasId.value) : null;
});

const kelasOptions = computed(() => [
    { value: '', label: '-- Pilih Kelas --' },
    ...(props.kelas || []).map(k => ({
        value: k.id,
        label: `${k.status_color === 'green' ? '✅' : (k.status_color === 'yellow' ? '⚠️' : '❌')} ${k.nama_kelas} - ${k.status_text}`
    }))
]);

const guruOptions = computed(() => [
    { value: '', label: '-- Belum Ada Guru --' },
    ...(props.gurus || []).map(g => ({ value: g.id, label: g.name }))
]);

watch(filterKelasId, (newId) => {
    router.get(route('pembelajaran.index'), { kelas_id: newId }, { preserveState: true });
});

const assignments = ref({});
const processingMapel = ref(null);
const showSuccessModal = ref(false);
const page = usePage();

// Initialize assignments from props
const initAssignments = () => {
    assignments.value = {};
    if (!props.mapels) return;
    
    props.mapels.forEach(m => {
        if (props.pembelajarans && props.pembelajarans[m.id]) {
            assignments.value[m.id] = props.pembelajarans[m.id].guru_id;
        } else {
            assignments.value[m.id] = '';
        }
    });
};

watch(() => props.pembelajarans, () => {
    initAssignments();
}, { deep: true });

onMounted(() => {
    initAssignments();
});

const saveAssignment = (mapelId) => {
    const guruId = assignments.value[mapelId];
    if (!filterKelasId.value) return;

    processingMapel.value = mapelId;
    router.post(route('pembelajaran.assign'), {
        kelas_id: filterKelasId.value,
        mapel_id: mapelId,
        guru_id: guruId
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            processingMapel.value = null;
            showSuccessModal.value = true;
            setTimeout(() => { showSuccessModal.value = false; }, 2000);
        },
        onError: () => {
            processingMapel.value = null;
        }
    });
};

const clearAssignment = (mapelId) => {
    assignments.value[mapelId] = '';
    saveAssignment(mapelId);
};

const isSavingBulk = ref(false);
const saveAll = () => {
    if (!filterKelasId.value) return;
    
    isSavingBulk.value = true;
    
    const assignmentsArray = Object.keys(assignments.value).map(mapelId => ({
        mapel_id: mapelId,
        guru_id: assignments.value[mapelId]
    }));

    router.post(route('pembelajaran.assignBulk'), {
        kelas_id: filterKelasId.value,
        assignments: assignmentsArray
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isSavingBulk.value = false;
            showSuccessModal.value = true;
            setTimeout(() => { showSuccessModal.value = false; }, 2000);
        },
        onError: () => {
            isSavingBulk.value = false;
        }
    });
};
</script>

<template>
    <Head title="Penugasan Mengajar" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Penugasan Mengajar" 
            :description="tahunAktif ? 'Tahun Ajaran: ' + tahunAktif.tahun + ' - ' + tahunAktif.semester : 'Belum ada tahun ajaran aktif.'"
            icon="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"
        />
        
        <div class="animate-fade-in space-y-6 max-w-[90rem] mx-auto pb-12">
            
            <!-- Tabel Data -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-2xl sm:rounded-[2rem] overflow-hidden shadow-2xl relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm relative z-10 flex flex-col xs:flex-row md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                    <div class="flex-1 w-full">
                        <label for="filter" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest mb-2"
                            :class="{
                                'text-rose-600 dark:text-rose-400': selectedKelas && selectedKelas.status_color === 'red',
                                'text-amber-600 dark:text-amber-400': selectedKelas && selectedKelas.status_color === 'yellow',
                                'text-emerald-600 dark:text-emerald-400': selectedKelas && selectedKelas.status_color === 'green',
                                'text-brand-600 dark:text-brand-400': !selectedKelas
                            }">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            <span class="hidden xs:inline">Pilih Kelas untuk Diatur Jadwalnya</span>
                            <span class="xs:hidden">Pilih Kelas</span>
                            <span v-if="selectedKelas" class="ml-auto text-[9px] px-2 py-0.5 rounded-md border"
                                :class="{
                                    'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-700': selectedKelas.status_color === 'red',
                                    'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-700': selectedKelas.status_color === 'yellow',
                                    'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700': selectedKelas.status_color === 'green'
                                }">
                                {{ selectedKelas.status_text }}
                            </span>
                        </label>
                        <div class="relative shadow-sm rounded-xl">
                            <SearchableSelect
                                v-model="filterKelasId"
                                :options="kelasOptions"
                                placeholder="-- Pilih Kelas --"
                                searchPlaceholder="Cari kelas..."
                                :error="selectedKelas && selectedKelas.status_color === 'red'"
                                :warning="selectedKelas && selectedKelas.status_color === 'yellow'"
                                :success="selectedKelas && selectedKelas.status_color === 'green'"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto -mx-4 sm:mx-0 relative z-10">
                    <table class="w-full text-left border-collapse bg-white/50 dark:bg-transparent rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700/50 min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <th class="px-4 sm:px-6 py-4 w-12 text-center">No</th>
                                <th class="px-4 sm:px-6 py-4">Mata Pelajaran</th>
                                <th class="px-4 sm:px-6 py-4 w-48 sm:w-56">Guru Pengampu</th>
                                <th class="px-4 sm:px-6 py-4 w-24 sm:w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr v-for="(mapel, index) in mapels" :key="mapel.id" :class="['hover:bg-slate-50/50 dark:hover:bg-slate-700/50 transition-colors group', !assignments[mapel.id] ? 'bg-rose-50/50 dark:bg-rose-900/20' : '']">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center font-bold text-slate-400 dark:text-slate-500">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="text-sm font-bold dark:text-white">{{ mapel.nama_mapel }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-0.5">Kelompok {{ mapel.kelompok }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <select
                                        v-model="assignments[mapel.id]"
                                        class="w-full text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 font-medium focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all"
                                    >
                                        <option value="">-- Pilih Guru --</option>
                                        <option v-for="g in gurus" :key="g.id" :value="g.id">{{ g.name }}</option>
                                    </select>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="saveAssignment(mapel.id)" :disabled="processingMapel === mapel.id" class="p-1.5 sm:p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 hover:text-emerald-700 dark:hover:text-emerald-300 rounded-lg transition-colors font-bold text-xs flex items-center justify-center min-w-[32px] disabled:opacity-50" title="Simpan Guru">
                                            <svg v-if="processingMapel === mapel.id" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button @click="clearAssignment(mapel.id)" :disabled="processingMapel === mapel.id || !assignments[mapel.id]" class="p-1.5 sm:p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 hover:text-rose-700 dark:hover:text-rose-300 rounded-lg transition-colors font-bold text-xs flex items-center justify-center disabled:opacity-50" title="Kosongkan Guru">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="mapels.length === 0">
                                <td colspan="4" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 mb-4">
                                        <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-600 dark:text-slate-300">Belum Ada Mapel</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="mapels.length > 0 && filterKelasId" class="px-4 sm:px-6 py-4 sm:py-5 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 flex justify-end">
                    <button @click="saveAll" :disabled="isSavingBulk" class="px-4 sm:px-6 py-2.5 sm:py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-black rounded-xl shadow-md shadow-brand-500/20 hover:shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5 transition-all flex items-center gap-2 disabled:opacity-70 text-xs sm:text-sm">
                        <svg v-if="isSavingBulk" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="hidden sm:inline">{{ isSavingBulk ? 'Menyimpan...' : 'Simpan Semua Penugasan' }}</span>
                        <span class="sm:hidden">{{ isSavingBulk ? 'Menyimpan...' : 'Simpan Semua' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Notif Toast (Fixed at bottom right) -->
        <transition enter-active-class="transition ease-out duration-300" enter-from-class="transform translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="transform translate-y-0 opacity-100" leave-to-class="transform translate-y-2 opacity-0">
            <div v-if="showSuccessModal" class="fixed bottom-4 right-4 sm:bottom-8 sm:right-8 z-50 flex items-center gap-2 sm:gap-3 bg-slate-800 dark:bg-slate-700 text-white px-4 sm:px-5 py-3 sm:py-4 rounded-xl sm:rounded-2xl shadow-xl border border-slate-700">
                <div class="h-7 w-7 sm:h-8 sm:w-8 rounded-full bg-emerald-500 flex items-center justify-center">
                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-xs sm:text-sm">Tersimpan!</h4>
                    <p class="text-[10px] sm:text-[11px] font-semibold text-slate-300">{{ page.props.flash.success || 'Data berhasil diperbarui.' }}</p>
                </div>
            </div>
        </transition>

    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeInAnim 0.15s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
@keyframes fadeInAnim { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { 0% { opacity: 0; transform: translateY(24px); } 100% { opacity: 1; transform: translateY(0); } }
</style>

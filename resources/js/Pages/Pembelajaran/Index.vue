<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl dark:text-white leading-tight tracking-tight">Penugasan Mengajar</h2>
                    <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">
                        Tahun Ajaran Aktif: <span class="text-brand-600">{{ tahunAktif ? tahunAktif.tahun + ' - ' + tahunAktif.semester : 'Belum Ada' }}</span>
                    </p>
                </div>
            </div>
        </template>
        
        <div class="animate-fade-in space-y-6 max-w-[90rem] mx-auto pb-12">
            
            <!-- Tabel Data -->
            <div class="bg-white border border-slate-200 dark:border-slate-700 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="p-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1 w-full md:max-w-md">
                        <label for="filter" class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest mb-2"
                            :class="{
                                'text-rose-600': selectedKelas && selectedKelas.status_color === 'red',
                                'text-amber-600': selectedKelas && selectedKelas.status_color === 'yellow',
                                'text-emerald-600': selectedKelas && selectedKelas.status_color === 'green',
                                'text-brand-600': !selectedKelas
                            }">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            Pilih Kelas untuk Diatur Jadwalnya
                            <span v-if="selectedKelas" class="ml-auto text-[9px] px-2 py-0.5 rounded-md border"
                                :class="{
                                    'bg-rose-100 text-rose-700 border-rose-200': selectedKelas.status_color === 'red',
                                    'bg-amber-100 text-amber-700 border-amber-200': selectedKelas.status_color === 'yellow',
                                    'bg-emerald-100 text-emerald-700 border-emerald-200': selectedKelas.status_color === 'green'
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

                <div class="overflow-x-auto relative z-10 p-2">
                    <table class="w-full text-left border-collapse bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4 w-1/3">Mata Pelajaran</th>
                                <th class="px-6 py-4">Guru Pengampu</th>
                                <th class="px-6 py-4 w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(mapel, index) in mapels" :key="mapel.id" class="hover:bg-slate-50/50 transition-colors group" :class="{'bg-rose-50/20': !assignments[mapel.id]}">
                                <td class="px-6 py-4 text-center font-bold text-slate-400 dark:text-slate-500">
                                    {{ index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold dark:text-white">{{ mapel.nama_mapel }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500 mt-0.5">Kelompok {{ mapel.kelompok }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <SearchableSelect
                                        v-model="assignments[mapel.id]"
                                        :options="guruOptions"
                                        placeholder="-- Belum Ada Guru --"
                                        searchPlaceholder="Cari guru..."
                                        :error="!assignments[mapel.id]"
                                        :success="!!assignments[mapel.id]"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="saveAssignment(mapel.id)" :disabled="processingMapel === mapel.id" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 rounded-lg transition-colors font-bold text-xs flex items-center justify-center min-w-[32px] disabled:opacity-50" title="Simpan Guru">
                                            <svg v-if="processingMapel === mapel.id" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button @click="clearAssignment(mapel.id)" :disabled="processingMapel === mapel.id || !assignments[mapel.id]" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 rounded-lg transition-colors font-bold text-xs flex items-center justify-center disabled:opacity-50" title="Kosongkan Guru">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="mapels.length === 0">
                                <td colspan="4" class="px-8 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-600 dark:text-slate-300">Belum Ada Mapel</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="mapels.length > 0 && filterKelasId" class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end">
                    <button @click="saveAll" :disabled="isSavingBulk" class="px-6 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-black rounded-xl shadow-md shadow-brand-500/20 hover:shadow-lg hover:shadow-brand-500/30 hover:-translate-y-0.5 transition-all flex items-center gap-2 disabled:opacity-70">
                        <svg v-if="isSavingBulk" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ isSavingBulk ? 'Menyimpan...' : 'Simpan Semua Penugasan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Notif Toast (Fixed at bottom right) -->
        <transition enter-active-class="transition ease-out duration-300" enter-from-class="transform translate-y-2 opacity-0" enter-to-class="transform translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="transform translate-y-0 opacity-100" leave-to-class="transform translate-y-2 opacity-0">
            <div v-if="showSuccessModal" class="fixed bottom-8 right-8 z-50 flex items-center gap-3 bg-slate-800 text-white px-5 py-4 rounded-2xl shadow-xl border border-slate-700">
                <div class="h-8 w-8 rounded-full bg-emerald-500 flex items-center justify-center">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm">Tersimpan!</h4>
                    <p class="text-[11px] font-semibold text-slate-300">{{ page.props.flash.success || 'Data berhasil diperbarui.' }}</p>
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

<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    tahunAktif: Object,
    kelas: Array,
    siswas: Array,
    kelasSudahInput: {
        type: Array,
        default: () => []
    },
    ekskuls: Array,
    filters: Object,
});

const form = useForm({
    kelas_id: props.filters.kelas_id || '',
    kehadiran: []
});

const showSuccessModal = ref(false);
const activeTab = ref('kehadiran');

const tabs = [
    {
        id: 'kehadiran',
        label: 'Kehadiran',
        shortLabel: 'Hadir',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        color: 'blue',
    },
    {
        id: 'ekskul',
        label: 'Ekstrakurikuler',
        shortLabel: 'Ekskul',
        icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        color: 'purple',
    },
    {
        id: 'catatan',
        label: 'Catatan',
        shortLabel: 'Catatan',
        icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        color: 'emerald',
    },
];

watch(() => props.siswas, (newSiswas) => {
    if (newSiswas && newSiswas.length > 0) {
        form.kehadiran = newSiswas.map(siswa => ({
            siswa_id: siswa.siswa_id,
            sakit: siswa.sakit !== null ? siswa.sakit : '',
            izin: siswa.izin !== null ? siswa.izin : '',
            alpa: siswa.alpa !== null ? siswa.alpa : '',
            ekskul_1: siswa.ekskul_1 || '',
            nilai_ekskul_1: siswa.nilai_ekskul_1 || '',
            ekskul_2: siswa.ekskul_2 || '',
            nilai_ekskul_2: siswa.nilai_ekskul_2 || '',
            ekskul_3: siswa.ekskul_3 || '',
            nilai_ekskul_3: siswa.nilai_ekskul_3 || '',
            catatan: siswa.catatan || '',
        }));
    } else {
        form.kehadiran = [];
    }
}, { immediate: true });

watch(() => form.kelas_id, (newKelasId) => {
    if (newKelasId) {
        router.reload({
            only: ['siswas', 'filters'],
            data: { kelas_id: newKelasId },
            preserveState: true,
            preserveScroll: true,
        });
    }
});

const page = usePage();

const submit = () => {
    form.post(route('kehadiran.storeBatch'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
        }
    });
};

const kelasOptions = computed(() => [
    { value: '', label: '-- Pilih kelas --' },
    ...(props.kelas || []).map(k => ({ value: k.id, label: k.nama_kelas })),
]);

const ekskulOptions = computed(() => [
    { value: '', label: '-- Tidak Ikut --' },
    ...(props.ekskuls || []).map(e => ({ value: e.nama_ekskul, label: e.nama_ekskul })),
]);

const nilaiEkskulOptions = [
    { value: '', label: '--' },
    { value: 'A', label: 'A (Sangat Baik)' },
    { value: 'B', label: 'B (Baik)' },
    { value: 'C', label: 'C (Cukup)' },
    { value: 'D', label: 'D (Kurang)' },
];

const tabColorClass = (tab, type) => {
    const colors = {
        blue: { active: 'text-blue-600 dark:text-blue-400 border-blue-500', icon: 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400', badge: 'bg-blue-500' },
        purple: { active: 'text-purple-600 dark:text-purple-400 border-purple-500', icon: 'bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400', badge: 'bg-purple-500' },
        emerald: { active: 'text-emerald-600 dark:text-emerald-400 border-emerald-500', icon: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400', badge: 'bg-emerald-500' },
    };
    return colors[tab.color][type];
};

const totalAbsenSiswa = (index) => {
    return (Number(form.kehadiran[index]?.sakit) || 0)
         + (Number(form.kehadiran[index]?.izin) || 0)
         + (Number(form.kehadiran[index]?.alpa) || 0);
};

const numberInputClass = "w-full text-center font-bold text-base bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-xs placeholder:text-slate-300 py-3";
const textInputClass = "w-full font-semibold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-300 py-2.5 px-3";
</script>

<template>
    <Head :title="$page.props.auth.user.role === 'admin' ? 'Monitoring Kehadiran' : 'Input Kehadiran & Ekskul'" />
    <AuthenticatedLayout>
        <PageHeader 
            :title="$page.props.auth.user.role === 'admin' ? 'Monitoring Kehadiran' : 'Input Kehadiran & Ekskul'"
            description="Kelola data absensi, ekstrakurikuler, dan catatan wali kelas."
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />

        <div class="animate-fade-in space-y-4 pb-24 md:pb-10">

            <!-- Notifikasi Flash -->
            <div v-if="page.props.flash && page.props.flash.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.success }}</span>
            </div>

            <div v-if="page.props.flash && page.props.flash.error" class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.error }}</span>
            </div>

            <!-- Panel Filter Kelas -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-2xl p-4 sm:p-6 shadow-xl">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Pilih Kelas
                </label>
                <SearchableSelect
                    v-model="form.kelas_id"
                    :options="kelasOptions"
                    placeholder="-- Pilih kelas --"
                    searchPlaceholder="Ketik nama kelas..."
                />
            </div>

            <!-- Area Data & Form -->
            <div v-if="siswas && siswas.length > 0" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-2xl overflow-hidden shadow-xl animate-slide-up">
                <form @submit.prevent="submit">
                    
                    <!-- Header -->
                    <div class="px-4 sm:px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 flex justify-between items-center gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-black text-sm">
                                {{ siswas.length }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-white">Siswa Terdaftar</p>
                                <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Tahun Ajaran Aktif</p>
                            </div>
                        </div>
                        <!-- Save button on desktop -->
                        <button type="submit" :disabled="form.processing"
                            class="hidden md:flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Semua Data' }}
                        </button>
                    </div>

                    <!-- Tab Navigation -->
                    <div class="px-4 sm:px-6 pt-4 border-b border-slate-100 dark:border-slate-700/50">
                        <div class="flex gap-1">
                            <button v-for="tab in tabs" :key="tab.id" type="button"
                                @click="activeTab = tab.id"
                                class="flex items-center gap-1.5 px-3 sm:px-5 py-2.5 sm:py-3 text-xs sm:text-sm font-bold rounded-t-xl transition-all border-b-2 -mb-px"
                                :class="activeTab === tab.id
                                    ? [tabColorClass(tab, 'active'), 'bg-white dark:bg-slate-800']
                                    : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50'">
                                <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 transition-colors"
                                    :class="activeTab === tab.id ? tabColorClass(tab, 'icon') : 'bg-slate-100 dark:bg-slate-700 text-slate-400'">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="tab.icon"/>
                                    </svg>
                                </div>
                                <span class="hidden sm:inline">{{ tab.label }}</span>
                                <span class="sm:hidden">{{ tab.shortLabel }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- ======================== TAB 1: KEHADIRAN ======================== -->
                    <div v-show="activeTab === 'kehadiran'">
                        <!-- Legend -->
                        <div class="px-4 sm:px-6 py-3 bg-blue-50/50 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/30 flex flex-wrap items-center gap-3 sm:gap-6">
                            <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Panduan:</p>
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400">
                                <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span> Sakit
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-amber-600 dark:text-amber-400">
                                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span> Izin
                            </span>
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400">
                                <span class="w-2 h-2 rounded-full bg-rose-400 shrink-0"></span> Alpa
                            </span>
                        </div>

                        <!-- MOBILE: Card layout -->
                        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-700/50">
                            <div v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                class="p-4 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                <!-- Siswa header -->
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-black uppercase shrink-0">
                                        {{ siswa.nama_siswa.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ siswa.nama_siswa }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Siswa #{{ index + 1 }}</p>
                                    </div>
                                    <!-- Total badge -->
                                    <div v-if="totalAbsenSiswa(index) > 0"
                                        class="ml-auto px-2.5 py-1 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 text-xs font-black">
                                        {{ totalAbsenSiswa(index) }}x Absen
                                    </div>
                                    <div v-else class="ml-auto px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-black">
                                        Hadir
                                    </div>
                                </div>
                                <!-- 3 columns input -->
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-black text-blue-500 uppercase tracking-widest mb-1 text-center">Sakit</label>
                                        <input type="number" min="0" v-model="form.kehadiran[index].sakit"
                                            :class="numberInputClass"
                                            class="focus:border-blue-400 focus:ring-blue-400/20"
                                            placeholder="0" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1 text-center">Izin</label>
                                        <input type="number" min="0" v-model="form.kehadiran[index].izin"
                                            :class="numberInputClass"
                                            class="focus:border-amber-400 focus:ring-amber-400/20"
                                            placeholder="0" />
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1 text-center">Alpa</label>
                                        <input type="number" min="0" v-model="form.kehadiran[index].alpa"
                                            :class="numberInputClass"
                                            class="focus:border-rose-400 focus:ring-rose-400/20"
                                            placeholder="0" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DESKTOP: Table layout -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        <th class="px-6 py-4 w-12 text-center">No</th>
                                        <th class="px-6 py-4">Nama Siswa</th>
                                        <th class="px-4 py-4 w-28 text-center">
                                            <span class="flex items-center justify-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span> Sakit
                                            </span>
                                        </th>
                                        <th class="px-4 py-4 w-28 text-center">
                                            <span class="flex items-center justify-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span> Izin
                                            </span>
                                        </th>
                                        <th class="px-4 py-4 w-28 text-center">
                                            <span class="flex items-center justify-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-rose-400 shrink-0"></span> Alpa
                                            </span>
                                        </th>
                                        <th class="px-6 py-4 w-28 text-center text-slate-300 dark:text-slate-600">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                    <tr v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                        class="hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors group">
                                        <td class="px-6 py-3 text-center">
                                            <span class="text-xs font-bold text-slate-300 dark:text-slate-600 group-hover:text-blue-400 transition-colors">{{ index + 1 }}</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/40 dark:to-indigo-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black uppercase shrink-0">
                                                    {{ siswa.nama_siswa.charAt(0) }}
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-white">{{ siswa.nama_siswa }}</span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            <input type="number" min="0" v-model="form.kehadiran[index].sakit"
                                                class="w-16 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-blue-400 focus:ring-4 focus:ring-blue-400/20 transition-all py-2.5 mx-auto block"
                                                placeholder="0" />
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            <input type="number" min="0" v-model="form.kehadiran[index].izin"
                                                class="w-16 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 transition-all py-2.5 mx-auto block"
                                                placeholder="0" />
                                        </td>
                                        <td class="px-2 py-3 text-center">
                                            <input type="number" min="0" v-model="form.kehadiran[index].alpa"
                                                class="w-16 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-rose-400 focus:ring-4 focus:ring-rose-400/20 transition-all py-2.5 mx-auto block"
                                                placeholder="0" />
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="text-sm font-black"
                                                :class="totalAbsenSiswa(index) > 0 ? 'text-rose-500' : 'text-slate-300 dark:text-slate-600'">
                                                {{ totalAbsenSiswa(index) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ======================== TAB 2: EKSTRAKURIKULER ======================== -->
                    <div v-show="activeTab === 'ekskul'">
                        <div class="px-4 sm:px-6 py-3 bg-purple-50/50 dark:bg-purple-900/10 border-b border-purple-100 dark:border-purple-900/30 flex items-center gap-3">
                            <p class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Panduan:</p>
                            <p class="text-xs font-semibold text-purple-600 dark:text-purple-400">Pilih ekskul dan nilai (A/B/C/D). Kosongkan jika tidak mengikuti.</p>
                        </div>

                        <!-- MOBILE: Card layout for Ekskul -->
                        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-700/50">
                            <div v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                class="p-4 hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-purple-100 to-fuchsia-100 dark:from-purple-900/40 dark:to-fuchsia-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm font-black uppercase shrink-0">
                                        {{ siswa.nama_siswa.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 dark:text-white">{{ siswa.nama_siswa }}</p>
                                        <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Ekskul</p>
                                    </div>
                                </div>
                                <!-- Ekskul 1, 2, 3 in stacked rows -->
                                <div class="space-y-3">
                                    <div v-for="n in [1,2,3]" :key="n" class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-500 font-black text-[9px] shrink-0">{{ n }}</span>
                                        <div class="flex-1 min-w-0">
                                            <SearchableSelect
                                                v-model="form.kehadiran[index][`ekskul_${n}`]"
                                                :options="ekskulOptions"
                                                placeholder="Pilih Ekskul..."
                                                searchPlaceholder="Cari..."
                                            />
                                        </div>
                                        <select v-model="form.kehadiran[index][`nilai_ekskul_${n}`]"
                                            class="w-16 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-purple-400 focus:ring-4 focus:ring-purple-400/20 transition-all py-2.5 shrink-0">
                                            <option v-for="opt in nilaiEkskulOptions" :key="opt.value" :value="opt.value">{{ opt.value || '--' }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DESKTOP: Table layout -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse" style="min-width: 700px;">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        <th class="px-6 py-4 w-12 text-center">No</th>
                                        <th class="px-6 py-4 w-44">Nama Siswa</th>
                                        <th class="px-4 py-4" colspan="2"><span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-md bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-500 font-black text-[9px]">1</span> Ekstrakurikuler 1</span></th>
                                        <th class="px-4 py-4" colspan="2"><span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-md bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-500 font-black text-[9px]">2</span> Ekstrakurikuler 2</span></th>
                                        <th class="px-4 py-4" colspan="2"><span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-md bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-500 font-black text-[9px]">3</span> Ekstrakurikuler 3</span></th>
                                    </tr>
                                    <tr class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                                        <th colspan="2"></th>
                                        <th class="px-3 py-2 w-48">Nama Kegiatan</th>
                                        <th class="px-2 py-2 w-16 text-center">Nilai</th>
                                        <th class="px-3 py-2 w-48">Nama Kegiatan</th>
                                        <th class="px-2 py-2 w-16 text-center">Nilai</th>
                                        <th class="px-3 py-2 w-48">Nama Kegiatan</th>
                                        <th class="px-2 py-2 w-16 text-center">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                    <tr v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                        class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-colors group">
                                        <td class="px-6 py-3 text-center"><span class="text-xs font-bold text-slate-300 dark:text-slate-600 group-hover:text-purple-400 transition-colors">{{ index + 1 }}</span></td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="h-7 w-7 rounded-full bg-gradient-to-br from-purple-100 to-fuchsia-100 dark:from-purple-900/40 dark:to-fuchsia-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-[10px] font-black uppercase shrink-0">{{ siswa.nama_siswa.charAt(0) }}</div>
                                                <span class="text-xs font-bold text-slate-700 dark:text-white truncate max-w-[120px]">{{ siswa.nama_siswa }}</span>
                                            </div>
                                        </td>
                                        <td class="px-2 py-3"><SearchableSelect v-model="form.kehadiran[index].ekskul_1" :options="ekskulOptions" placeholder="Pilih..." searchPlaceholder="Cari..." /></td>
                                        <td class="px-2 py-3"><select v-model="form.kehadiran[index].nilai_ekskul_1" class="w-14 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl py-2.5 pr-6"><option v-for="opt in nilaiEkskulOptions" :key="opt.value" :value="opt.value">{{ opt.value || '--' }}</option></select></td>
                                        <td class="px-2 py-3"><SearchableSelect v-model="form.kehadiran[index].ekskul_2" :options="ekskulOptions" placeholder="Pilih..." searchPlaceholder="Cari..." /></td>
                                        <td class="px-2 py-3"><select v-model="form.kehadiran[index].nilai_ekskul_2" class="w-14 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl py-2.5 pr-6"><option v-for="opt in nilaiEkskulOptions" :key="opt.value" :value="opt.value">{{ opt.value || '--' }}</option></select></td>
                                        <td class="px-2 py-3"><SearchableSelect v-model="form.kehadiran[index].ekskul_3" :options="ekskulOptions" placeholder="Pilih..." searchPlaceholder="Cari..." /></td>
                                        <td class="px-2 py-3"><select v-model="form.kehadiran[index].nilai_ekskul_3" class="w-14 text-center font-bold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl py-2.5 pr-6"><option v-for="opt in nilaiEkskulOptions" :key="opt.value" :value="opt.value">{{ opt.value || '--' }}</option></select></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ======================== TAB 3: CATATAN ======================== -->
                    <div v-show="activeTab === 'catatan'">
                        <div class="px-4 sm:px-6 py-3 bg-emerald-50/50 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900/30 flex items-center gap-3">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Panduan:</p>
                            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Isi catatan khusus untuk setiap siswa. Kosongkan jika tidak ada catatan.</p>
                        </div>

                        <!-- MOBILE: Card layout for Catatan -->
                        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-700/50">
                            <div v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                class="p-4 hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-black uppercase shrink-0">
                                        {{ siswa.nama_siswa.charAt(0) }}
                                    </div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-white">{{ siswa.nama_siswa }}</p>
                                </div>
                                <textarea v-model="form.kehadiran[index].catatan"
                                    rows="3"
                                    class="w-full font-semibold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 transition-all placeholder:font-normal placeholder:text-slate-400 py-3 px-4 resize-none"
                                    placeholder="Tulis catatan untuk siswa ini... (opsional)"
                                ></textarea>
                            </div>
                        </div>

                        <!-- DESKTOP: Table layout -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        <th class="px-6 py-4 w-12 text-center">No</th>
                                        <th class="px-6 py-4 w-52">Nama Siswa</th>
                                        <th class="px-6 py-4">Catatan Wali Kelas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                    <tr v-for="(siswa, index) in siswas" :key="siswa.siswa_id"
                                        class="hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10 transition-colors group">
                                        <td class="px-6 py-3 text-center"><span class="text-xs font-bold text-slate-300 dark:text-slate-600 group-hover:text-emerald-400 transition-colors">{{ index + 1 }}</span></td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-black uppercase shrink-0">{{ siswa.nama_siswa.charAt(0) }}</div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-white">{{ siswa.nama_siswa }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <textarea v-model="form.kehadiran[index].catatan"
                                                rows="1"
                                                class="w-full font-semibold text-sm bg-slate-50 dark:bg-slate-900/50 dark:text-white border border-slate-200 dark:border-slate-700/50 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 transition-all placeholder:font-normal placeholder:text-slate-300 py-2.5 px-3 resize-none"
                                                placeholder="Tulis catatan... (opsional)"
                                            ></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Simpan (Desktop) -->
                    <div class="hidden md:flex px-6 py-5 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30 justify-between items-center gap-3">
                        <p class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                            <span class="font-black text-slate-600 dark:text-slate-300">Tips:</span> Satu tombol simpan menyimpan data semua tab sekaligus.
                        </p>
                        <button type="submit" :disabled="form.processing"
                            class="px-8 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Semua Data' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Empty State - Kelas dipilih tapi tidak ada siswa -->
            <div v-else-if="form.kelas_id" class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-3xl p-12 text-center shadow-sm animate-slide-up">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-300 dark:text-slate-600 mb-4">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-700 dark:text-white mb-1">Belum Ada Siswa</h3>
                <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Tidak ada data siswa yang terdaftar di kelas ini.</p>
            </div>
            
            <!-- Empty State - Belum pilih kelas -->
            <div v-else class="flex flex-col items-center justify-center py-16 opacity-50">
                <svg class="h-16 w-16 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Silakan pilih Kelas terlebih dahulu</p>
            </div>
        </div>

        <!-- MOBILE: Floating Save Button -->
        <teleport to="body">
            <div v-if="siswas && siswas.length > 0" class="md:hidden fixed bottom-0 left-0 right-0 z-50 p-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border-t border-slate-200 dark:border-slate-700 shadow-2xl">
                <button @click="submit" :disabled="form.processing"
                    class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-black rounded-2xl shadow-lg shadow-brand-500/40 active:scale-95 transition-all flex items-center justify-center gap-3 disabled:opacity-70">
                    <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Semua Data' }}
                </button>
            </div>
        </teleport>

        <!-- Custom Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false">
            <div class="p-6 sm:p-8 text-center relative overflow-hidden dark:bg-slate-800">
                <div class="mx-auto flex items-center justify-center h-16 w-16 sm:h-20 sm:w-20 rounded-full bg-green-50 dark:bg-green-900/20 mb-4 sm:mb-6 shadow-inner border border-emerald-100 relative z-10">
                    <svg class="h-8 w-8 sm:h-10 sm:w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-lg sm:text-2xl font-black dark:text-white mb-2">Berhasil Disimpan!</h2>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 mb-6 sm:mb-8">
                    {{ page.props.flash?.success || 'Seluruh data absensi, ekstrakurikuler, dan catatan telah berhasil disimpan.' }}
                </p>
                <button @click="showSuccessModal = false" class="px-6 sm:px-8 py-2.5 sm:py-3 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                    Tutup Jendela
                </button>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeInAnim 0.15s ease-out forwards;
}
.animate-slide-up {
    animation: slideUp 0.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes fadeInAnim {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    0% { opacity: 0; transform: translateY(24px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Hilangkan panah spinner di input number */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>

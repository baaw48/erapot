<script setup>
import { ref } from 'vue';
import { useForm, Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    sekolah: Object,
    tahunAjarans: Array,
    raporSetting: Object,
});

const page = usePage();

const form = useForm({
    nama_sekolah: props.sekolah.nama_sekolah || '',
    alamat: props.sekolah.alamat || '',
    kepala_sekolah: props.sekolah.kepala_sekolah || '',
    nip_kepsek: props.sekolah.nip_kepsek || '',
    npsn: props.sekolah.npsn || '',
    website: props.sekolah.website || '',
    email: props.sekolah.email || '',
    jenis_asesmen: props.sekolah.jenis_asesmen || 'ASTS',
    logo: null,
});

const showSuccessModal = ref(false);

const showDeleteTaModal = ref(false);
const itemToDeleteTa = ref(null);

const showActivateTaModal = ref(false);
const itemToActivateTa = ref(null);

const submit = () => {
    form.post(route('sekolah.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
        }
    });
};

const isEditingTa = ref(false);
const editingTaId = ref(null);

const formTa = useForm({
    tahun: '',
    semester: 'Ganjil',
});

const editTahun = (ta) => {
    isEditingTa.value = true;
    editingTaId.value = ta.id;
    formTa.tahun = ta.tahun;
    formTa.semester = ta.semester;
    window.scrollTo({ top: document.getElementById('form-ta').offsetTop - 100, behavior: 'smooth' });
};

const cancelEditTa = () => {
    isEditingTa.value = false;
    editingTaId.value = null;
    formTa.reset();
    formTa.clearErrors();
};

const submitTa = () => {
    if (isEditingTa.value) {
        formTa.put(route('tahun-ajaran.update', editingTaId.value), {
            preserveScroll: true,
            onSuccess: () => cancelEditTa(),
        });
    } else {
        formTa.post(route('tahun-ajaran.store'), {
            preserveScroll: true,
            onSuccess: () => cancelEditTa(),
        });
    }
};

const deleteTahun = (id) => {
    itemToDeleteTa.value = id;
    showDeleteTaModal.value = true;
};

const executeDeleteTa = () => {
    if (itemToDeleteTa.value) {
        router.delete(route('tahun-ajaran.destroy', itemToDeleteTa.value), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteTaModal.value = false;
                itemToDeleteTa.value = null;
            }
        });
    }
};

const closeDeleteTaModal = () => {
    showDeleteTaModal.value = false;
    itemToDeleteTa.value = null;
};

const setActiveTa = (id) => {
    itemToActivateTa.value = id;
    showActivateTaModal.value = true;
};

const executeActivateTa = () => {
    if (itemToActivateTa.value) {
        router.post(route('tahun-ajaran.setActive', itemToActivateTa.value), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showActivateTaModal.value = false;
                itemToActivateTa.value = null;
            }
        });
    }
};

const closeActivateTaModal = () => {
    showActivateTaModal.value = false;
    itemToActivateTa.value = null;
};

const inputClass = "w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-semibold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400";

// Rapor Settings Form
const formRapor = useForm({
    show_peringkat: props.raporSetting?.show_peringkat ?? true,
    show_kehadiran: props.raporSetting?.show_kehadiran ?? true,
    show_ekskul: props.raporSetting?.show_ekskul ?? true,
    show_catatan: props.raporSetting?.show_catatan ?? true,
    show_deskripsi: props.raporSetting?.show_deskripsi ?? true,
    show_kepribadian: props.raporSetting?.show_kepribadian ?? true,
});

const submitRapor = () => {
    formRapor.post(route('sekolah.raporSetting'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
        }
    });
};
</script>

<template>
    <Head title="Pengaturan Sekolah" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tight">Pengaturan Sistem</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Identitas Sekolah & Tahun Ajaran</p>
                </div>
            </div>
        </template>

        <div class="animate-fade-in space-y-8 max-w-6xl mx-auto pb-12">
            
            <div v-if="page.props.flash && page.props.flash.success" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.success }}</span>
            </div>

            <div v-if="page.props.flash && page.props.flash.error" class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="font-bold text-sm">{{ page.props.flash.error }}</span>
            </div>

            <!-- Form Identitas Sekolah -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="px-8 py-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10 flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-brand-100 text-brand-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Identitas Sekolah</h3>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-0.5">Informasi utama untuk Kop Rapor</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="p-8 relative z-10 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="nama_sekolah" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Sekolah</label>
                            <input id="nama_sekolah" v-model="form.nama_sekolah" type="text" :class="inputClass" required placeholder="Contoh: MTs Al-Hasanah" />
                        </div>
                        <div class="space-y-2">
                            <label for="npsn" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">NPSN</label>
                            <input id="npsn" v-model="form.npsn" type="text" :class="inputClass" placeholder="Nomor Pokok Sekolah Nasional" />
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label for="alamat" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Alamat Lengkap</label>
                            <input id="alamat" v-model="form.alamat" type="text" :class="inputClass" placeholder="Jalan, Desa, Kecamatan, Kab/Kota" />
                        </div>
                        <div class="space-y-2">
                            <label for="kepala_sekolah" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Kepala Sekolah</label>
                            <input id="kepala_sekolah" v-model="form.kepala_sekolah" type="text" :class="inputClass" placeholder="Nama lengkap beserta gelar" />
                        </div>
                        <div class="space-y-2">
                            <label for="nip_kepsek" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">NIP Kepala Sekolah</label>
                            <input id="nip_kepsek" v-model="form.nip_kepsek" type="text" :class="inputClass" placeholder="(Opsional)" />
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Email Sekolah</label>
                            <input id="email" v-model="form.email" type="email" :class="inputClass" placeholder="admin@sekolah.sch.id" />
                        </div>
                        <div class="space-y-2">
                            <label for="website" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Website</label>
                            <input id="website" v-model="form.website" type="text" :class="inputClass" placeholder="www.sekolah.sch.id" />
                        </div>
                        
                        <div class="md:col-span-2 space-y-2 p-6 bg-brand-50 border border-brand-100 rounded-2xl">
                            <label for="jenis_asesmen" class="flex items-center gap-2 text-xs font-black text-brand-700 uppercase tracking-widest">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Judul Penilaian Aktif
                            </label>
                            <input id="jenis_asesmen" v-model="form.jenis_asesmen" type="text" class="w-full bg-white border border-brand-200 text-brand-900 rounded-xl px-4 py-3 font-black focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20 transition-all text-lg" placeholder="Misal: ASTS, ASAS, PTS, PAT" required />
                            <p class="text-xs font-semibold text-brand-600 mt-2">Judul ini akan digunakan secara otomatis di Kop Rapor dan judul halaman Nilai.</p>
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label for="logo" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Logo Sekolah</label>
                            <div class="flex items-center gap-6 p-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:bg-slate-100 transition-colors">
                                <div class="shrink-0 relative group">
                                    <div class="h-24 w-24 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center overflow-hidden">
                                        <img v-if="page.props.sekolah.logo_url" :src="page.props.sekolah.logo_url" alt="Logo Sekolah" class="h-full w-full object-contain p-2" />
                                        <svg v-else class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <input id="logo" type="file" @input="form.logo = $event.target.files[0]" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer" accept="image/*" />
                                    <p class="text-xs text-slate-400 mt-2 font-medium">Format: JPG, PNG. Maksimal ukuran file: 2MB.</p>
                                    <div v-if="form.errors.logo" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.logo }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Identitas Sekolah' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Tahun Pelajaran -->
            <div id="form-ta" class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.2s; animation-fill-mode: both;">
                <div class="px-8 py-6 border-b border-slate-100 bg-white flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Manajemen Tahun Pelajaran</h3>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-0.5">Kelola semester dan tahun aktif</p>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Form Tambah/Edit TA -->
                    <form @submit.prevent="submitTa" class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-8 relative">
                        <h4 class="text-sm font-black text-slate-700 mb-4">{{ isEditingTa ? 'Edit Data Semester' : 'Tambah Semester Baru' }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">
                            <div class="md:col-span-5 space-y-2">
                                <label for="tahun" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Tahun Ajaran</label>
                                <input id="tahun" v-model="formTa.tahun" type="text" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 font-bold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all" placeholder="Cth: 2025/2026" required />
                                <div v-if="formTa.errors.tahun" class="text-rose-500 text-xs font-bold">{{ formTa.errors.tahun }}</div>
                            </div>
                            <div class="md:col-span-4 space-y-2">
                                <label for="semester" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Semester</label>
                                <select id="semester" v-model="formTa.semester" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 font-bold focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all" required>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                <div v-if="formTa.errors.semester" class="text-rose-500 text-xs font-bold">{{ formTa.errors.semester }}</div>
                            </div>
                            <div class="md:col-span-3 space-y-2 flex flex-col justify-end h-full mt-2 md:mt-0">
                                <div class="flex gap-2 w-full h-[42px] mt-6">
                                    <button type="submit" :disabled="formTa.processing" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-colors shadow-md hover:shadow-lg disabled:opacity-70 flex items-center justify-center">
                                        {{ isEditingTa ? 'Update' : 'Tambah' }}
                                    </button>
                                    <button v-if="isEditingTa" type="button" @click="cancelEditTa" class="px-4 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabel TA -->
                    <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-6 py-4 w-16 text-center">No.</th>
                                    <th class="px-6 py-4">Tahun Ajaran</th>
                                    <th class="px-6 py-4">Semester</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="(ta, index) in tahunAjarans" :key="ta.id" class="transition-colors group" :class="{'bg-emerald-50/30': ta.is_active, 'hover:bg-slate-50': !ta.is_active}">
                                    <td class="px-6 py-4 text-center text-sm font-bold text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-black text-slate-800">{{ ta.tahun }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-600">{{ ta.semester }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="ta.is_active" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Aktif Saat Ini
                                        </span>
                                        <button v-else @click="setActiveTa(ta.id)" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 text-[10px] font-black uppercase tracking-widest border border-transparent transition-all">
                                            Jadikan Aktif
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="editTahun(ta)" class="p-2 text-brand-500 hover:bg-brand-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteTahun(ta.id)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus" :disabled="ta.is_active" :class="{'opacity-30 cursor-not-allowed': ta.is_active}">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="tahunAjarans.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-sm font-bold text-slate-400">Belum ada data Tahun Pelajaran.</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Rapor -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Pengaturan Pencetakan Rapor</h3>
                        <p class="text-xs font-semibold text-slate-400">Pilih data yang tampil di rapor</p>
                    </div>
                </div>

                <form @submit.prevent="submitRapor" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-700">Tampilkan Peringkat</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="formRapor.show_peringkat" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:bg-white after:rounded-full after:h-5 after:w-5 after:top-0.5 after:left-[2px] after:transition-all"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-700">Tampilkan Kehadiran</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="formRapor.show_kehadiran" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:bg-white after:rounded-full after:h-5 after:w-5 after:top-0.5 after:left-[2px] after:transition-all"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-700">Tampilkan Ekskul</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="formRapor.show_ekskul" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:bg-white after:rounded-full after:h-5 after:w-5 after:top-0.5 after:left-[2px] after:transition-all"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-slate-700">Tampilkan Catatan</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="formRapor.show_catatan" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-checked:bg-brand-600 rounded-full peer peer-checked:after:translate-x-full after:absolute after:bg-white after:rounded-full after:h-5 after:w-5 after:top-0.5 after:left-[2px] after:transition-all"></div>
                        </label>
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" :disabled="formRapor.processing" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ formRapor.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Custom Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-50 mb-6 shadow-inner border border-emerald-100 relative z-10">
                    <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-2 relative z-10">
                    Pengaturan Tersimpan!
                </h2>
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10">
                    Data profil sekolah berhasil diperbarui.
                </p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup Jendela
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Delete TA Modal -->
        <Modal :show="showDeleteTaModal" @close="closeDeleteTaModal" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-rose-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6 relative z-10">
                    <svg class="h-10 w-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 relative z-10">Konfirmasi Hapus</h2>
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10">
                    Apakah Anda yakin ingin menghapus <strong class="text-rose-600">Tahun Pelajaran</strong> ini?
                </p>
                <div class="flex gap-3 relative z-10">
                    <button @click="closeDeleteTaModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-all">
                        Batal
                    </button>
                    <button @click="executeDeleteTa" class="flex-1 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Activate TA Modal -->
        <Modal :show="showActivateTaModal" @close="closeActivateTaModal" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-amber-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-amber-50 mb-6 relative z-10">
                    <svg class="h-10 w-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 relative z-10">Aktifkan Semester?</h2>
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10 leading-relaxed">
                    Perhatian! Mengaktifkan Tahun Pelajaran ini akan merubah halaman input nilai dan fitur lain agar menyesuaikan dengan periode semester ini. <br/><strong class="text-amber-600">Lanjutkan?</strong>
                </p>
                <div class="flex gap-3 relative z-10">
                    <button @click="closeActivateTaModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-all">
                        Batal
                    </button>
                    <button @click="executeActivateTa" class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Ya, Aktifkan
                    </button>
                </div>
            </div>
        </Modal>
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

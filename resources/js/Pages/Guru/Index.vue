<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    gurus: Object,
    mapels: Array,
    kelas: Array,
    gurusMapel: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || 10);

let searchTimeout = null;
watch([searchQuery, perPage], ([newSearch, newPerPage]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('guru.index'), { search: newSearch, per_page: newPerPage }, { preserveState: true, preserveScroll: true });
    }, 300);
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const showImportModal = ref(false);
const itemToDelete = ref(null);
const isEditing = ref(false);
const editingId = ref(null);

const formImport = useForm({
    file: null,
});

const openImportModal = () => {
    formImport.reset();
    formImport.clearErrors();
    showImportModal.value = true;
};

const closeImportModal = () => {
    showImportModal.value = false;
};

const submitImport = () => {
    formImport.post(route('guru.import'), {
        onSuccess: () => {
            closeImportModal();
            showSuccessModal.value = true;
        },
    });
};

const form = useForm({
    name: '',
    username: '',
    nip: '',
    mapel_diampu: [],
    kelas_diampu: '',
    password: '',
    password_confirmation: '',
});

const mapelWarnings = computed(() => {
    let warnings = [];
    if (!form.mapel_diampu || form.mapel_diampu.length === 0) return warnings;
    
    form.mapel_diampu.forEach(selectedMapel => {
        props.gurusMapel.forEach(g => {
            if (isEditing.value && g.id === editingId.value) return;
            if (g.mapel_diampu) {
                let gMapels = g.mapel_diampu.split(',').map(m => m.trim());
                if (gMapels.includes(selectedMapel)) {
                    warnings.push(`Mapel "${selectedMapel}" juga diajarkan oleh ${g.name}.`);
                }
            }
        });
    });
    return warnings;
});

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.mapel_diampu = [];
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (guru) => {
    isEditing.value = true;
    editingId.value = guru.id;
    form.name = guru.name;
    form.username = guru.username;
    form.nip = guru.nip || '';
    form.mapel_diampu = guru.mapel_diampu ? guru.mapel_diampu.split(', ') : [];
    form.kelas_diampu = guru.kelas_diampu || '';
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('guru.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessModal.value = true;
            },
        });
    } else {
        form.post(route('guru.store'), {
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessModal.value = true;
            },
        });
    }
};

const confirmDelete = (id) => {
    itemToDelete.value = id;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (itemToDelete.value) {
        router.delete(route('guru.destroy', itemToDelete.value), {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false;
                itemToDelete.value = null;
            }
        });
    }
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    itemToDelete.value = null;
};
</script>

<template>
    <Head title="Data Master Guru" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tight">Data Guru</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kelola Pengguna & Tenaga Pendidik</p>
                </div>
            </div>
        </template>
        
        <div class="animate-fade-in space-y-6 max-w-[90rem] mx-auto pb-12">
            
            <div v-if="$page.props.flash && $page.props.flash.success" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="p-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10">
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="flex-1 relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Cari NIP, nama guru, atau mapel..." 
                                class="w-full pl-11 pr-11 py-3 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium placeholder:text-slate-400"
                            >
                            <button 
                                v-if="searchQuery" 
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-rose-500 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="w-full md:w-32 shrink-0">
                            <div class="relative">
                                <select id="perPage" v-model="perPage" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="10">10 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="100">100 Baris</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden md:block text-right mr-2">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Guru</p>
                            <p class="text-xl font-black text-brand-600">{{ gurus.total }}</p>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto">
                            <button @click="openImportModal" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 text-sm font-bold rounded-xl shadow-sm transition-all">
                                <svg class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <span class="hidden md:inline">Import</span>
                            </button>
                            <a :href="route('guru.export')" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 text-sm font-bold rounded-xl shadow-sm transition-all">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4 4m0 0l-4-4m4 4V4" /></svg>
                                <span class="hidden md:inline">Export</span>
                            </a>
                            <button @click="openAddModal" class="inline-flex items-center justify-center flex-1 md:flex-none gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Guru
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-4 w-16 text-center">No.</th>
                                <th class="px-8 py-4">Nama & NIP</th>
                                <th class="px-8 py-4">Akun Login</th>
                                <th class="px-8 py-4">Kelas Diampu (Wali)</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(guru, index) in gurus.data" :key="guru.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-5 text-center text-sm font-bold text-slate-400">
                                    {{ (gurus.current_page - 1) * gurus.per_page + index + 1 }}
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-black shadow-inner uppercase border border-slate-200">
                                            {{ guru.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ guru.name }}</div>
                                            <div class="text-[11px] font-bold text-slate-400 mt-0.5">{{ guru.nip ? 'NIP. ' + guru.nip : 'Non-NIP' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ guru.username }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span v-if="guru.kelas_diampu" class="inline-flex items-center px-2 py-1 bg-purple-50 text-purple-700 text-[10px] font-black uppercase tracking-wider rounded-md border border-purple-100">
                                        {{ guru.kelas_diampu }}
                                    </span>
                                    <span v-else class="text-xs font-medium text-slate-400 italic">-</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(guru)" class="p-2.5 text-brand-500 hover:bg-brand-50 rounded-xl transition-colors" title="Edit Guru">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(guru.id)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors" title="Hapus Guru">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="gurus.data.length === 0">
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-600">Belum Ada Guru</h3>
                                    <p class="text-xs font-medium text-slate-400 mt-1">Silakan tambahkan data guru baru.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginasi -->
                <div class="px-8 py-4 border-t border-slate-100 bg-slate-50" v-if="gurus.links && gurus.links.length > 3">
                    <Pagination :links="gurus.links" />
                </div>
            </div>
        </div>
        
        <!-- Form Modal (Tambah/Edit) -->
        <Modal :show="showModal" @close="closeModal" maxWidth="2xl">
            <div class="p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-48 h-48 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-8 relative z-10">
                    <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg v-if="isEditing" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">{{ isEditing ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Lengkapi profil dan penugasan</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6 relative z-10">
                    <div class="flex items-start gap-3 bg-indigo-50/50 border border-indigo-100 text-indigo-700 px-5 py-4 rounded-2xl">
                        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-semibold">Kosongkan password jika tidak ingin mengubah password lama.</p>
                    </div>

                    <div v-if="isEditing" class="space-y-2">
                        <label for="password" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Password Baru (Opsional)</label>
                        <input id="password" v-model="form.password" type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" placeholder="Minimal 6 karakter" />
                        <div v-if="form.errors.password" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div v-if="isEditing && form.password" class="space-y-2">
                        <label for="password_confirmation" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Konfirmasi Password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" placeholder="Ulangi password" required />
                        <div v-if="form.errors.password_confirmation" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Lengkap (Gelar)</label>
                            <input id="name" v-model="form.name" type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="Cth: Budi Santoso, S.Pd" />
                            <div v-if="form.errors.name" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="space-y-2">
                            <label for="nip" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">NIP (Opsional)</label>
                            <input id="nip" v-model="form.nip" type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" placeholder="1980xxxxxx" />
                            <div v-if="form.errors.nip" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.nip }}</div>
                        </div>
                        
                        <div class="space-y-2 md:col-span-2">
                            <label for="username" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Username Login</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input id="username" v-model="form.username" type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="budi123" />
                            </div>
                            <div v-if="form.errors.username" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.username }}</div>
                        </div>
                    </div>

                    <hr class="border-slate-100">
                    
                    <div class="grid grid-cols-1 gap-6">

                        <div class="space-y-2">
                            <label for="kelas_diampu" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Wali Kelas (Opsional)</label>
                            <select id="kelas_diampu" v-model="form.kelas_diampu" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer">
                                <option value="" class="text-slate-400">-- Bukan Wali Kelas --</option>
                                <option v-for="k in kelas" :key="k.id" :value="k.nama_kelas">{{ k.nama_kelas }}</option>
                            </select>
                            <p class="text-[11px] font-semibold text-slate-400 mt-1">Pilih kelas jika guru ini ditugaskan sebagai wali kelas.</p>
                            <div v-if="form.errors.kelas_diampu" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.kelas_diampu }}</div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-3.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center justify-center gap-2">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-rose-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6 relative z-10">
                    <svg class="h-10 w-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-xl font-black text-slate-800 mb-2 relative z-10">Konfirmasi Hapus</h2>
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10">
                    Apakah Anda yakin ingin menghapus data guru ini? <strong class="text-rose-600">Data yang sudah dihapus tidak dapat dikembalikan.</strong>
                </p>
                <div class="flex gap-3 relative z-10">
                    <button @click="closeDeleteModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-bold rounded-xl transition-all">
                        Batal
                    </button>
                    <button @click="executeDelete" class="flex-1 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-50 mb-6 shadow-inner border border-emerald-100 relative z-10">
                    <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 mb-2 relative z-10">Berhasil!</h2>
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10">
                    {{ $page.props.flash.success || 'Data berhasil disimpan.' }}
                </p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Import Modal -->
        <Modal :show="showImportModal" @close="closeImportModal" maxWidth="md">
            <div class="p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Import Data Guru</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Upload file excel/csv</p>
                    </div>
                </div>

                <div class="relative z-10">
                    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs font-bold text-amber-800 mb-2">Panduan Import:</p>
                        <ul class="text-xs text-amber-700 space-y-1 list-disc pl-4 font-medium">
                            <li>Pastikan format file sesuai dengan template.</li>
                            <li>Kolom NIP jika kosong, sistem akan meng-generate username/password otomatis.</li>
                            <li>Untuk Mapel Diampu, pisahkan dengan koma jika lebih dari satu.</li>
                        </ul>
                        <a href="/download_guru.php" target="_blank" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 hover:text-brand-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download Template Excel
                        </a>
                    </div>

                    <form @submit.prevent="submitImport" class="space-y-4">
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Pilih File</label>
                            <input type="file" @input="formImport.file = $event.target.files[0]" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required />
                            <div v-if="formImport.errors.file" class="text-rose-500 text-xs font-bold mt-1">{{ formImport.errors.file }}</div>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-100 mt-6">
                            <button type="button" @click="closeImportModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-all">
                                Batal
                            </button>
                            <button type="submit" :disabled="formImport.processing" class="flex-1 px-4 py-3 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center justify-center gap-2">
                                <svg v-if="formImport.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ formImport.processing ? 'Mengimport...' : 'Upload & Import' }}
                            </button>
                        </div>
                    </form>
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

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    admins: Object,
    filters: Object,
});

const page = usePage();
const currentUser = page.props.auth.user;

const searchQuery = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || 10);

let searchTimeout = null;
watch([searchQuery, perPage], ([newSearch, newPerPage]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.index'), { search: newSearch, per_page: newPerPage }, { preserveState: true, preserveScroll: true });
    }, 300);
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const showExportAkunModal = ref(false);

const openExportAkun = (withReset) => {
    const url = route('admin.exportAkun') + (withReset ? '?reset_password=1' : '');
    window.open(url, '_blank');
    showExportAkunModal.value = false;
};
const itemToDelete = ref(null);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    username: '',
    password: '',
});

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (admin) => {
    isEditing.value = true;
    editingId.value = admin.id;
    form.name = admin.name;
    form.username = admin.username;
    form.password = ''; // Kosongkan password saat edit
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessModal.value = true;
            },
        });
    } else {
        form.post(route('admin.store'), {
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
        router.delete(route('admin.destroy', itemToDelete.value), {
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
    <Head title="Manajemen Admin" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Manajemen Admin" 
            description="Kelola akun dengan akses penuh ke seluruh sistem."
            icon="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"
        />
        
        <div class="animate-fade-in space-y-6 max-w-[90rem] mx-auto pb-12">
            
            <div v-if="$page.props.flash && $page.props.flash.success" class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ $page.props.flash.success }}</span>
            </div>
            
            <div v-if="$page.props.flash && $page.props.flash.error" class="flex items-center gap-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ $page.props.flash.error }}</span>
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/50 p-5 rounded-2xl shadow-sm flex items-start gap-4 animate-slide-up">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-indigo-800 dark:text-indigo-400 uppercase tracking-widest mb-1">Akses Penuh</h4>
                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 leading-relaxed">
                        Akun Admin memiliki wewenang untuk mengelola semua data sekolah, tahun ajaran, dan pengguna lainnya. Pastikan Anda hanya memberikan akses ini kepada staf yang berwenang.
                    </p>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-[2rem] overflow-hidden shadow-2xl relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/30 backdrop-blur-sm relative z-10">
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="flex-1 relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 group-focus-within:text-brand-500 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="Cari nama atau username admin..." 
                                class="w-full pl-11 pr-11 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 text-slate-700 dark:text-white dark:text-white dark:text-slate-200 rounded-xl focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all font-medium placeholder:text-slate-400 dark:text-slate-500"
                            >
                            <button 
                                v-if="searchQuery" 
                                @click="searchQuery = ''"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-slate-500 hover:text-rose-500 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="w-full md:w-32 shrink-0">
                            <div class="relative">
                                <select id="perPage" v-model="perPage" class="w-full bg-slate-50 dark:bg-slate-800/50 dark:bg-slate-800/50 border border-slate-200 text-slate-700 dark:text-white dark:text-white dark:text-slate-200 rounded-xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="10">10 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="100">100 Baris</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden md:block text-right mr-2">
                            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Total Admin</p>
                            <p class="text-xl font-black text-brand-600">{{ admins.total }}</p>
                        </div>
                        <div class="flex gap-2 w-full md:w-auto flex-wrap">
                            <button @click="showExportAkunModal = true" class="inline-flex items-center justify-center flex-1 md:flex-none gap-1.5 px-4 py-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 text-sm font-bold rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Export Akun
                            </button>
                            <button @click="openAddModal" class="inline-flex items-center justify-center flex-1 md:flex-none gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Admin
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700/50 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-4 w-16 text-center">No.</th>
                                <th class="px-8 py-4">Nama Lengkap</th>
                                <th class="px-8 py-4">Username</th>
                                <th class="px-8 py-4">Role</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                            <tr v-for="(admin, index) in admins.data" :key="admin.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-5 text-center text-sm font-bold text-slate-400 dark:text-slate-500">
                                    {{ (admins.current_page - 1) * admins.per_page + index + 1 }}
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 text-slate-500 dark:text-slate-400 dark:text-slate-500 flex items-center justify-center font-black shadow-inner uppercase border border-slate-200">
                                            {{ admin.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold dark:text-white flex items-center gap-2">
                                                {{ admin.name }}
                                                <span v-if="admin.id === currentUser.id" class="text-[9px] px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded-md uppercase tracking-wider font-black">Anda</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-600">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ admin.username }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center px-2 py-1 bg-brand-50 dark:bg-brand-900/30 text-brand-700 dark:text-brand-400 text-[10px] font-black uppercase tracking-wider rounded-md border border-brand-100 dark:border-brand-800">
                                        Admin
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(admin)" class="p-2.5 text-brand-500 hover:bg-brand-50 rounded-xl transition-colors" title="Edit Admin">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button v-if="admin.id !== currentUser.id" @click="confirmDelete(admin.id)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors" title="Hapus Admin">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="admins.data.length === 0">
                                <td colspan="5" class="px-8 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-slate-800/50 text-slate-300 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-600 dark:text-slate-300">Belum Ada Admin Lain</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginasi -->
                        <div class="px-8 py-4 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/50" v-if="admins.links && admins.links.length > 3">
                    <Pagination :links="admins.links" />
                </div>
            </div>
        </div>
        
        <!-- Form Modal (Tambah/Edit) -->
        <Modal :show="showModal" @close="closeModal" maxWidth="md">
            <div class="p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-48 h-48 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-8 relative z-10">
                    <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black dark:text-white">{{ isEditing ? 'Edit Data Admin' : 'Tambah Admin Baru' }}</h2>
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mt-0.5">Lengkapi profil akses penuh</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6 relative z-10">
                    <div class="space-y-2">
                        <label for="name" class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">Nama Lengkap</label>
                        <input id="name" v-model="form.name" type="text" class="w-full bg-slate-50 dark:bg-slate-800/50 dark:bg-slate-800/50 border border-slate-200 dark:text-white rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400 dark:text-slate-500" required placeholder="Cth: Budi Santoso" />
                        <div v-if="form.errors.name" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.name }}</div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="username" class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">Username Login</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input id="username" v-model="form.username" type="text" class="w-full bg-slate-50 dark:bg-slate-800/50 dark:bg-slate-800/50 border border-slate-200 dark:text-white rounded-2xl pl-11 pr-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400 dark:text-slate-500" required placeholder="admin_budi" />
                        </div>
                        <div v-if="form.errors.username" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.username }}</div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 dark:text-slate-500 uppercase tracking-widest">Password</label>
                        <input id="password" v-model="form.password" type="password" class="w-full bg-slate-50 dark:bg-slate-800/50 dark:bg-slate-800/50 border border-slate-200 dark:text-white rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400 dark:text-slate-500" :required="!isEditing" placeholder="Minimal 8 karakter" />
                        <p v-if="isEditing" class="text-[10px] font-semibold text-slate-400 dark:text-slate-500 mt-1">*Kosongkan jika tidak ingin mengubah password.</p>
                        <div v-if="form.errors.password" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.password }}</div>
                    </div>
                    
                    <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-3.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-all">
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
                <h2 class="text-xl font-black dark:text-white mb-2 relative z-10">Konfirmasi Hapus</h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 mb-8 relative z-10">
                    Apakah Anda yakin ingin menghapus admin ini? <strong class="text-rose-600">Akses admin mereka akan segera dicabut.</strong>
                </p>
                <div class="flex gap-3 relative z-10">
                    <button @click="closeDeleteModal" class="flex-1 px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-all">
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
                <h2 class="text-2xl font-black dark:text-white mb-2 relative z-10">Berhasil!</h2>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 mb-8 relative z-10">
                    {{ $page.props.flash.success || 'Data berhasil disimpan.' }}
                </p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </Modal>

        </Modal>

        <!-- Export Akun Modal -->
        <Modal :show="showExportAkunModal" @close="showExportAkunModal = false" maxWidth="sm">
            <div class="p-6 dark:bg-slate-800">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-800 dark:text-white">Export Daftar Akun Admin</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pilih cara export yang diinginkan</p>
                    </div>
                </div>

                <div class="space-y-3 mb-5">
                    <!-- Option 1: Without reset -->
                    <button @click="openExportAkun(false)" class="w-full text-left p-4 rounded-xl border-2 border-slate-200 dark:border-slate-600 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 dark:hover:border-blue-600 transition-all group">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg shrink-0 group-hover:bg-blue-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-sm text-slate-800 dark:text-white">Cetak Daftar Username Saja</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Hanya menampilkan username tanpa mengubah password.</div>
                            </div>
                        </div>
                    </button>

                    <!-- Option 2: With reset -->
                    <button @click="openExportAkun(true)" class="w-full text-left p-4 rounded-xl border-2 border-slate-200 dark:border-slate-600 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 dark:hover:border-amber-600 transition-all group">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-lg shrink-0 group-hover:bg-amber-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-sm text-slate-800 dark:text-white">Reset Password + Cetak Daftar Akun</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Generate password baru otomatis untuk semua admin (kecuali akun Anda sendiri).</div>
                                <div class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-black text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 rounded-md">
                                    ⚠️ Password admin lain akan berubah!
                                </div>
                            </div>
                        </div>
                    </button>
                </div>

                <button @click="showExportAkunModal = false" class="w-full px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Batal
                </button>
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

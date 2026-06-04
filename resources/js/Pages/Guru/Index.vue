<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    gurus: Object,
    mapels: Array,
    kelas: Array,
    gurusMapel: Array,
    filters: Object,
});

const page = usePage();
const flash = computed(() => page.props.flash);

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

const formImport = useForm({ file: null });

const openImportModal = () => {
    formImport.reset();
    formImport.clearErrors();
    showImportModal.value = true;
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

const closeModal = () => showModal.value = false;
const closeImportModal = () => showImportModal.value = false;

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
</script>

<template>
    <Head title="Data Guru" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Data Guru" 
            description="Kelola data guru, wali kelas, dan penugasan mata pelajaran."
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
        />

        <div class="space-y-6">

            <!-- Flash -->
            <div v-if="flash?.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.success }}</span>
            </div>
            <div v-if="flash?.error" class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-5 py-4 rounded-2xl shadow-sm">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.error }}</span>
            </div>

            <!-- Main Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl sm:rounded-[2rem] shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden hover:shadow-primary-500/5 transition-all duration-300">
                <!-- Header -->
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200/50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30">
                    <!-- Mobile: Stacked layout -->
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <!-- Filters - side by side on mobile -->
                        <div class="flex flex-col xs:flex-row gap-2 sm:gap-3 flex-1 w-full">
                            <!-- Search -->
                            <div class="flex-1 min-w-0">
                                <div class="relative">
                                    <input v-model="searchQuery" type="text" placeholder="Cari..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all pl-10 placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                                    <svg class="h-4 w-4 text-slate-400 dark:text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 dark:text-slate-500 hover:text-rose-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Tampilkan - side by side on mobile -->
                            <div class="w-full xs:w-auto xs:flex-1 xs:max-w-[140px]">
                                <div class="relative">
                                    <select v-model="perPage" class="w-full dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200 bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <svg class="h-4 w-4 text-slate-400 dark:text-slate-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Actions - compact on mobile -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs text-slate-400 dark:text-slate-500 lg:hidden">Total:</span>
                            <span class="text-sm lg:text-lg font-bold text-primary-600 lg:hidden">{{ gurus.total }}</span>
                            <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1 hidden lg:block"></div>
                            <button @click="openImportModal" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 text-xs sm:text-sm font-medium rounded-xl transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <span class="hidden sm:inline">Import</span>
                            </button>
                            <a :href="route('guru.export')" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-600 text-xs sm:text-sm font-medium rounded-xl transition-all">
                                <svg class="h-4 w-4 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4 4m0 0l-4-4m4 4V4" /></svg>
                                <span class="hidden sm:inline">Export</span>
                            </a>
                            <button @click="openAddModal" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span class="hidden xs:inline">Tambah</span>
                            </button>
                        </div>
                    </div>

                    <!-- Total - only on desktop -->
                    <div class="hidden lg:flex justify-end mt-3 pt-3 border-t border-slate-200/50 dark:border-slate-700/50">
                        <span class="text-sm text-slate-500 dark:text-slate-400">Total: </span>
                        <span class="text-lg font-bold text-primary-600 ml-1">{{ gurus.total }}</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto -mx-4 sm:mx-0">
                    <table class="w-full text-left min-w-[600px] sm:min-w-0">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="px-4 sm:px-6 py-3.5 text-center w-12">No</th>
                                <th class="px-4 sm:px-6 py-3.5">Nama & NIP</th>
                                <th class="px-4 sm:px-6 py-3.5 hidden xs:table-cell">Username</th>
                                <th class="px-4 sm:px-6 py-3.5 hidden sm:table-cell">Wali Kelas</th>
                                <th class="px-4 sm:px-6 py-3.5 text-right w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr v-for="(guru, index) in gurus.data" :key="guru.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 sm:px-6 py-3 text-center text-sm font-medium text-slate-400 dark:text-slate-500">
                                    {{ (gurus.current_page - 1) * gurus.per_page + index + 1 }}
                                </td>
                                <td class="px-4 sm:px-6 py-3">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg sm:rounded-xl bg-gradient-to-br from-primary-100 dark:from-primary-900/50 to-primary-200 dark:to-primary-800 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold shadow-inner text-xs sm:text-sm shrink-0">
                                            {{ guru.name.charAt(0) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-700 dark:text-white truncate max-w-[120px] sm:max-w-none">{{ guru.name }}</div>
                                            <div class="text-xs text-slate-400 dark:text-slate-500 xs:hidden">{{ guru.nip ? 'NIP. ' + guru.nip : '-' }}</div>
                                            <div class="hidden xs:block text-xs text-slate-400 dark:text-slate-500 sm:hidden">{{ guru.nip ? 'NIP. ' + guru.nip : '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 hidden xs:table-cell">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ guru.username }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 hidden sm:table-cell">
                                    <span v-if="guru.kelas_diampu" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium border border-purple-100 dark:border-purple-800">
                                        {{ guru.kelas_diampu }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400 dark:text-slate-500">-</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openEditModal(guru)" class="p-1.5 sm:p-2 text-slate-400 dark:text-slate-500 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-lg transition-all" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(guru.id)" class="p-1.5 sm:p-2 text-slate-400 dark:text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all" title="Hapus">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="gurus.data.length === 0">
                                <td colspan="5" class="px-4 sm:px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 mb-3 sm:mb-4">
                                        <svg class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <p class="font-semibold text-slate-500 dark:text-slate-400 text-sm sm:text-base">Belum ada data guru</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 hidden sm:block">Tambahkan data guru baru</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50" v-if="gurus.links?.length > 3">
                    <Pagination :links="gurus.links" />
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="md">
            <div class="p-4 sm:p-6 dark:bg-slate-800">
                <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="p-2 sm:p-3 bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 rounded-lg sm:rounded-xl">
                        <svg v-if="isEditing" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">{{ isEditing ? 'Edit Guru' : 'Tambah Guru Baru' }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Lengkapi profil dan penugasan</p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 sm:p-4 mb-4 sm:mb-5">
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">Kosongkan password jika tidak ingin mengubah.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-3 sm:space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Nama Lengkap</label>
                            <input v-model="form.name" type="text" required placeholder="Nama guru..." class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            <div v-if="form.errors.name" class="text-xs text-danger-500 mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">NIP</label>
                            <input v-model="form.nip" type="text" placeholder="1980xxxxxx" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            <div v-if="form.errors.nip" class="text-xs text-danger-500 mt-1">{{ form.errors.nip }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Username</label>
                        <input v-model="form.username" type="text" required placeholder="username login" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                        <div v-if="form.errors.username" class="text-xs text-danger-500 mt-1">{{ form.errors.username }}</div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Password <span v-if="isEditing" class="text-slate-400 dark:text-slate-500 font-normal normal-case">(opsional)</span></label>
                            <input v-model="form.password" type="password" :required="!isEditing" placeholder="Minimal 6 karakter" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            <div v-if="form.errors.password" class="text-xs text-danger-500 mt-1">{{ form.errors.password }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Konfirmasi</label>
                            <input v-model="form.password_confirmation" type="password" :required="form.password" placeholder="Ulangi password" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500" />
                            <div v-if="form.errors.password_confirmation" class="text-xs text-danger-500 mt-1">{{ form.errors.password_confirmation }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Wali Kelas</label>
                        <select v-model="form.kelas_diampu" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer">
                            <option value="">-- Bukan Wali Kelas --</option>
                            <option v-for="k in kelas" :key="k.id" :value="k.nama_kelas">{{ k.nama_kelas }}</option>
                        </select>
                        <div v-if="form.errors.kelas_diampu" class="text-xs text-danger-500 mt-1">{{ form.errors.kelas_diampu }}</div>
                    </div>

                    <div class="flex gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-slate-100 dark:border-slate-700 mt-4 sm:mt-5">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-2.5 sm:py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
            <div class="p-4 sm:p-6 dark:bg-slate-800 text-center">
                <div class="mx-auto w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-danger-50 dark:bg-danger-900/30 flex items-center justify-center mb-3 sm:mb-4">
                    <svg class="h-6 w-6 sm:h-8 sm:w-8 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Konfirmasi Hapus</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 sm:mb-6">Yakin hapus guru ini? Data tidak dapat dikembalikan.</p>
                <div class="flex gap-2 sm:gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                    <button @click="executeDelete" class="flex-1 px-4 py-2.5 sm:py-3 bg-danger-500 text-white font-medium rounded-xl hover:bg-danger-600 transition-all">Hapus</button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-4 sm:p-6 dark:bg-slate-800 text-center">
                <div class="mx-auto w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-success-50 dark:bg-success-900/30 flex items-center justify-center mb-3 sm:mb-4 shadow-inner">
                    <svg class="h-6 w-6 sm:h-8 sm:w-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Berhasil!</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 sm:mb-5">{{ flash?.success || 'Data berhasil disimpan.' }}</p>
                <button @click="showSuccessModal = false" class="px-6 sm:px-8 py-2.5 sm:py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all">Tutup</button>
            </div>
        </Modal>

        <!-- Import Modal -->
        <Modal :show="showImportModal" @close="closeImportModal" maxWidth="md">
            <div class="p-4 sm:p-6 dark:bg-slate-800">
                <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-5">
                    <div class="p-2 sm:p-3 bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 rounded-lg sm:rounded-xl">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">Import Data Guru</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Upload file Excel/CSV</p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 sm:p-4 mb-4 sm:mb-5">
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 mb-2">Panduan:</p>
                    <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-1 list-disc pl-4">
                        <li>Gunakan format sesuai template</li>
                        <li>Kosongkan NIP untuk auto-generate</li>
                    </ul>
                    <a href="/download_guru.php" target="_blank" class="mt-2 sm:mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download Template
                    </a>
                </div>

                <form @submit.prevent="submitImport" class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Pilih File</label>
                        <input type="file" @input="formImport.file = $event.target.files[0]" accept=".csv,.xlsx,.xls" required class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-3 sm:file:mr-4 file:py-2 file:sm:py-2.5 file:px-3 sm:file:px-4 file:rounded-lg sm:file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/50 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900 cursor-pointer border border-slate-200 dark:border-slate-600 rounded-xl p-2.5 sm:p-3" />
                        <div v-if="formImport.errors.file" class="text-xs text-danger-500 mt-1">{{ formImport.errors.file }}</div>
                    </div>
                    <div class="flex gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-slate-100 dark:border-slate-700 mt-4 sm:mt-5">
                        <button type="button" @click="closeImportModal" class="flex-1 px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-all">Batal</button>
                        <button type="submit" :disabled="formImport.processing" class="flex-1 px-4 py-2.5 sm:py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                            <svg v-if="formImport.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ formImport.processing ? 'Mengimport...' : 'Import' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
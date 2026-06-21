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
const showExportAkunModal = ref(false);

const openExportAkun = (withReset) => {
    const url = route('guru.exportAkun') + (withReset ? '?reset_password=1' : '');
    window.open(url, '_blank');
    showExportAkunModal.value = false;
};
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

        <div class="px-4 pb-6 space-y-4">
            <!-- Flash -->
            <div v-if="flash?.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.success }}</span>
            </div>
            <div v-if="flash?.error" class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.error }}</span>
            </div>

            <!-- Header Mobile -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <input v-model="searchQuery" type="text" placeholder="Cari guru..." class="flex-1 sm:w-64 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl px-4 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button @click="showExportAkunModal = true" class="px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 flex items-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Export Akun
                    </button>
                    <button @click="openImportModal" class="px-3 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600">
                        Import
                    </button>
                    <button @click="openAddModal" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700">
                        + Tambah
                    </button>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="px-4 py-3.5 text-center w-16">No</th>
                                <th class="px-4 py-3.5">Nama & NIP</th>
                                <th class="px-4 py-3.5 text-center">Username</th>
                                <th class="px-4 py-3.5">Wali Kelas</th>
                                <th class="px-4 py-3.5 text-right w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr v-for="(guru, index) in gurus.data" :key="guru.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4 text-center text-sm font-medium text-slate-400 dark:text-slate-500">
                                    {{ (gurus.current_page - 1) * gurus.per_page + index + 1 }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-100 dark:from-primary-900/50 to-primary-200 dark:to-primary-800 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold shadow-inner text-lg">
                                            {{ guru.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-700 dark:text-white">{{ guru.name }}</div>
                                            <div class="text-xs text-slate-400 dark:text-slate-500">{{ guru.nip ? 'NIP. ' + guru.nip : '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium">
                                        {{ guru.username }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <span v-if="guru.kelas_diampu" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 text-xs font-medium">
                                        {{ guru.kelas_diampu }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400 dark:text-slate-500">-</span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openEditModal(guru)" class="p-2 text-slate-400 dark:text-slate-500 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-lg transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(guru.id)" class="p-2 text-slate-400 dark:text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="gurus.data.length === 0">
                                <td colspan="5" class="px-4 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <p class="font-semibold text-slate-500 dark:text-slate-400">Belum ada data guru</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50" v-if="gurus.links?.length > 3">
                    <Pagination :links="gurus.links" />
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-3">
                <div v-for="(guru, index) in gurus.data" :key="guru.id" class="bg-white dark:bg-slate-800 rounded-xl shadow-md border border-slate-200/50 dark:border-slate-700/50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-primary-100 dark:from-primary-900/50 to-primary-200 dark:to-primary-800 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-xl shadow-inner">
                                {{ guru.name.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-base">{{ guru.name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ guru.username }}</span>
                                    <span v-if="guru.nip" class="text-xs text-slate-500 dark:text-slate-400">NIP: {{ guru.nip }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button @click="openEditModal(guru)" class="p-2 text-slate-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/30 rounded-lg transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button @click="confirmDelete(guru.id)" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    <div v-if="guru.kelas_diampu" class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span class="text-sm text-slate-500 dark:text-slate-400">Wali Kelas:</span>
                            <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ guru.kelas_diampu }}</span>
                        </div>
                    </div>
                </div>

                <!-- Empty State Mobile -->
                <div v-if="gurus.data.length === 0" class="bg-white dark:bg-slate-800 rounded-xl shadow-md border border-slate-200/50 dark:border-slate-700/50 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 mb-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <p class="font-semibold text-slate-500 dark:text-slate-400">Belum ada data guru</p>
                    <button @click="openAddModal" class="mt-4 px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700">
                        Tambah Guru Baru
                    </button>
                </div>

                <!-- Pagination Mobile -->
                <div v-if="gurus.links?.length > 3" class="flex justify-center">
                    <Pagination :links="gurus.links" />
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="sm">
            <div class="p-5 dark:bg-slate-800">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2.5 bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 rounded-xl">
                        <svg v-if="isEditing" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">{{ isEditing ? 'Edit Guru' : 'Tambah Guru Baru' }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Lengkapi profil</p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 mb-4">
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">Kosongkan password jika tidak ingin mengubah.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Nama</label>
                            <input v-model="form.name" type="text" required class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">NIP</label>
                            <input v-model="form.nip" type="text" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Username</label>
                        <input v-model="form.username" type="text" required class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Password</label>
                            <input v-model="form.password" type="password" :required="!isEditing" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Konfirmasi</label>
                            <input v-model="form.password_confirmation" type="password" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1 block">Wali Kelas</label>
                        <select v-model="form.kelas_diampu" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3.5 py-2.5 text-sm font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer">
                            <option value="">-- Bukan Wali --</option>
                            <option v-for="k in kelas" :key="k.id" :value="k.nama_kelas">{{ k.nama_kelas }}</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-3 border-t border-slate-100 dark:border-slate-700 mt-4">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600">Batal</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all disabled:opacity-60 flex items-center justify-center gap-2 text-sm">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
            <div class="p-5 text-center dark:bg-slate-800">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-danger-50 dark:bg-danger-900/30 flex items-center justify-center mb-4">
                    <svg class="h-7 w-7 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">Konfirmasi Hapus</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5">Yakin hapus guru ini?</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600">Batal</button>
                    <button @click="executeDelete" class="flex-1 px-4 py-2.5 bg-danger-500 text-white font-medium rounded-xl hover:bg-danger-600 text-sm">Hapus</button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-5 text-center dark:bg-slate-800">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-success-50 dark:bg-success-900/30 flex items-center justify-center mb-4 shadow-inner">
                    <svg class="h-7 w-7 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white mb-2">Berhasil!</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">{{ flash?.success || 'Data berhasil disimpan.' }}</p>
                <button @click="showSuccessModal = false" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 text-sm">Tutup</button>
            </div>
        </Modal>

        <!-- Import Modal -->
        <Modal :show="showImportModal" @close="closeImportModal" maxWidth="sm">
            <div class="p-5 dark:bg-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2.5 bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Import Data Guru</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Upload file Excel/CSV</p>
                    </div>
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 mb-4">
                    <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-1 list-disc pl-4">
                        <li>Gunakan template Excel</li>
                        <li>Kosongkan NIP untuk auto</li>
                    </ul>
                    <a href="/download_guru.php" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600 dark:text-primary-400">
                        Download Template
                    </a>
                </div>

                <form @submit.prevent="submitImport" class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Pilih File</label>
                        <input type="file" @input="formImport.file = $event.target.files[0]" accept=".csv,.xlsx,.xls" required class="block w-full text-sm text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 rounded-xl p-2.5 cursor-pointer" />
                    </div>
                    <div class="flex gap-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="closeImportModal" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600">Batal</button>
                        <button type="submit" :disabled="formImport.processing" class="flex-1 px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-xl text-sm">Import</button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Export Akun Modal -->
        <Modal :show="showExportAkunModal" @close="showExportAkunModal = false" maxWidth="sm">
            <div class="p-6 dark:bg-slate-800">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-black text-slate-800 dark:text-white">Export Daftar Akun Guru</h2>
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
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Hanya menampilkan username tanpa mengubah password. Aman dan tidak merusak akun yang sudah ada.</div>
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
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Generate password baru otomatis untuk semua guru, update di database, lalu cetak daftarnya.</div>
                                <div class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-black text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 rounded-md">
                                    ⚠️ Semua password guru akan berubah!
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
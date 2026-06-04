<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    kelas: Object,
    gurus: Array,
    filters: Object,
});

const page = usePage();
const flash = computed(() => page.props.flash);

const perPage = ref(props.filters?.per_page || 10);
watch(perPage, (newPerPage) => {
    router.get(route('kelas.index'), { per_page: newPerPage }, { preserveState: true, preserveScroll: true });
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const itemToDelete = ref(null);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    nama_kelas: '',
    tingkat: '7',
    wali_kelas_id: '',
    kktp: 75,
});

const guruOptions = computed(() => [
    { value: '', label: '-- Kosong / Belum Ada --' },
    ...(props.gurus || []).map(g => ({ value: g.id, label: g.name })),
]);

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (k) => {
    isEditing.value = true;
    editingId.value = k.id;
    form.nama_kelas = k.nama_kelas;
    form.tingkat = k.tingkat.toString();
    form.wali_kelas_id = k.wali_kelas_id || '';
    form.kktp = k.kktp || 75;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => showModal.value = false;

const submit = () => {
    if (isEditing.value) {
        form.put(route('kelas.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessModal.value = true;
            },
        });
    } else {
        form.post(route('kelas.store'), {
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
        router.delete(route('kelas.destroy', itemToDelete.value), {
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
    <Head title="Data Kelas" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Data Kelas" 
            description="Kelola data rombongan belajar, wali kelas, dan batas ketuntasan."
            icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
        />

        <div class="space-y-6">

            <!-- Flash -->
            <div v-if="flash?.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.success }}</span>
            </div>

            <!-- Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl sm:rounded-[2rem] shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden hover:shadow-primary-500/5 transition-all duration-300">
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-200/50 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                    <div>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Daftar Rombongan Belajar</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 dark:text-slate-500 dark:text-slate-400 dark:text-slate-500 mt-0.5">{{ kelas.total }} kelas terdaftar</p>
                    </div>
                    <button @click="openAddModal" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">Tambah</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-center w-16">No</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4">Nama Kelas</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-center hidden xs:table-cell">Tingkat</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 hidden sm:table-cell">Wali Kelas</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-center hidden sm:table-cell">KKTP</th>
                                <th class="px-4 sm:px-6 py-3 sm:py-4 text-right w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr v-for="(k, index) in kelas.data" :key="k.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center text-sm font-medium text-slate-400 dark:text-slate-500">
                                    {{ (kelas.current_page - 1) * kelas.per_page + index + 1 }}
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 sm:h-10 sm:w-10 rounded-lg sm:rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold shadow-inner">
                                            {{ k.nama_kelas.charAt(0) }}
                                        </div>
                                        <span class="font-semibold text-slate-700 dark:text-slate-200">{{ k.nama_kelas }}</span>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center hidden xs:table-cell">
                                    <span class="inline-flex items-center justify-center px-2.5 sm:px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold">{{ k.tingkat }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 hidden sm:table-cell">
                                    <div v-if="k.wali_kelas" class="flex items-center gap-2">
                                        <div class="h-6 w-6 sm:h-7 sm:w-7 rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-[10px] sm:text-xs font-bold uppercase border border-white dark:border-slate-600 shadow-sm">
                                            {{ k.wali_kelas.name.charAt(0) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ k.wali_kelas.name }}</span>
                                    </div>
                                    <span v-else class="text-xs text-slate-400 dark:text-slate-500">-</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-center hidden sm:table-cell">
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 sm:px-2.5 sm:py-1 rounded-lg border border-blue-100 dark:border-blue-800">{{ k.kktp }}</span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 sm:py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openEditModal(k)" class="p-2 text-slate-400 dark:text-slate-500 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(k.id)" class="p-2 text-slate-400 dark:text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="kelas.data.length === 0">
                                <td colspan="6" class="px-4 sm:px-6 py-12 sm:py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 mb-4">
                                        <svg class="h-7 w-7 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <p class="font-semibold text-slate-500 dark:text-slate-400">Belum ada kelas</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Tambahkan kelas baru</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50" v-if="kelas.links?.length > 3">
                    <Pagination :links="kelas.links" />
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="md">
            <div class="p-4 sm:p-6 dark:bg-slate-800">
                <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="p-2.5 sm:p-3 bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 rounded-xl">
                        <svg v-if="isEditing" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white">{{ isEditing ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Lengkapi formulir</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Nama Kelas</label>
                        <input v-model="form.nama_kelas" type="text" required placeholder="Contoh: VII-A, VIII-1" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all text-sm sm:text-base" />
                        <div v-if="form.errors.nama_kelas" class="text-xs text-danger-500 mt-1">{{ form.errors.nama_kelas }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Tingkat</label>
                            <select v-model="form.tingkat" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer text-sm sm:text-base">
                                <option value="7">Kelas 7</option>
                                <option value="8">Kelas 8</option>
                                <option value="9">Kelas 9</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">KKTP</label>
                            <input v-model="form.kktp" type="number" min="0" max="100" class="w-full bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100 rounded-xl px-3 sm:px-4 py-2.5 sm:py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all text-sm sm:text-base" />
                            <div v-if="form.errors.kktp" class="text-xs text-danger-500 mt-1">{{ form.errors.kktp }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 block">Wali Kelas</label>
                        <SearchableSelect v-model="form.wali_kelas_id" :options="guruOptions" placeholder="-- Kosong / Belum Ada --" searchPlaceholder="Ketik nama guru..." />
                        <div v-if="form.errors.wali_kelas_id" class="text-xs text-danger-500 mt-1">{{ form.errors.wali_kelas_id }}</div>
                    </div>

                    <div class="flex gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-slate-100 dark:border-slate-700 mt-4 sm:mt-5">
                        <button type="button" @click="closeModal" class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-all text-sm">Batal</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-60 flex items-center justify-center gap-2 text-sm">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
            <div class="p-4 sm:p-6 text-center dark:bg-slate-800">
                <div class="mx-auto w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-danger-50 dark:bg-danger-900/30 flex items-center justify-center mb-4">
                    <svg class="h-7 w-7 sm:h-8 sm:w-8 text-danger-500 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Konfirmasi Hapus</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-5 sm:mb-6">Yakin hapus kelas ini? Data siswa & nilai ikut terhapus.</p>
                <div class="flex gap-2 sm:gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition-all text-sm">Batal</button>
                    <button @click="executeDelete" class="flex-1 px-3 sm:px-4 py-2.5 sm:py-3 bg-danger-500 text-white font-medium rounded-xl hover:bg-danger-600 transition-all text-sm">Hapus</button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-4 sm:p-6 text-center dark:bg-slate-800">
                <div class="mx-auto w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-success-50 dark:bg-success-900/30 flex items-center justify-center mb-4 shadow-inner">
                    <svg class="h-7 w-7 sm:h-8 sm:w-8 text-success-500 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white mb-2">Berhasil!</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 sm:mb-5">Data kelas berhasil disimpan.</p>
                <button @click="showSuccessModal = false" class="px-6 sm:px-8 py-2.5 sm:py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all text-sm">Tutup</button>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
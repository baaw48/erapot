<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    mapels: Object,
    filters: Object,
});

const page = usePage();
const flash = computed(() => page.props.flash);

const perPage = ref(props.filters?.per_page || 10);
watch(perPage, (newPerPage) => {
    router.get(route('mapel.index'), { per_page: newPerPage }, { preserveState: true, preserveScroll: true });
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const itemToDelete = ref(null);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    nama_mapel: '',
    kelompok: 'A',
    urutan: 1,
});

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (mapel) => {
    isEditing.value = true;
    editingId.value = mapel.id;
    form.nama_mapel = mapel.nama_mapel;
    form.kelompok = mapel.kelompok || 'A';
    form.urutan = mapel.urutan || 1;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => showModal.value = false;

const submit = () => {
    if (isEditing.value) {
        form.put(route('mapel.update', editingId.value), {
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessModal.value = true;
            },
        });
    } else {
        form.post(route('mapel.store'), {
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
        router.delete(route('mapel.destroy', itemToDelete.value), {
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
    <Head title="Mata Pelajaran" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-primary-100 text-primary-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-slate-800">Mata Pelajaran</h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">Kelola Data Mapel</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">

            <!-- Flash -->
            <div v-if="flash?.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-xl">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ flash.success }}</span>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-slate-800">Daftar Mata Pelajaran</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ mapels.total }} mapel terdaftar</p>
                    </div>
                    <button @click="openAddModal" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4 text-center w-16">No</th>
                                <th class="px-6 py-4">Mata Pelajaran</th>
                                <th class="px-6 py-4 text-center">Kelompok</th>
                                <th class="px-6 py-4 text-center">Urutan</th>
                                <th class="px-6 py-4 text-right w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(mapel, index) in mapels.data" :key="mapel.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-sm font-medium text-slate-400">
                                    {{ (mapels.current_page - 1) * mapels.per_page + index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-secondary-50 text-secondary-600 flex items-center justify-center shadow-inner">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ mapel.nama_mapel }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-secondary-50 text-secondary-700 text-xs font-semibold border border-secondary-100">
                                        {{ mapel.kelompok || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-semibold text-sm">{{ mapel.urutan || 0 }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openEditModal(mapel)" class="p-2 text-primary-500 hover:bg-primary-50 rounded-lg transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(mapel.id)" class="p-2 text-danger-500 hover:bg-danger-50 rounded-lg transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="mapels.data.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 text-slate-300 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                    <p class="font-semibold text-slate-500">Belum ada mata pelajaran</p>
                                    <p class="text-xs text-slate-400 mt-1">Tambahkan mapel baru</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50" v-if="mapels.links?.length > 3">
                    <Pagination :links="mapels.links" />
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="md">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-primary-100 text-primary-600 rounded-xl">
                        <svg v-if="isEditing" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">{{ isEditing ? 'Edit Mapel' : 'Tambah Mapel' }}</h2>
                        <p class="text-xs text-slate-500">Lengkapi formulir</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Mapel</label>
                        <input v-model="form.nama_mapel" type="text" required placeholder="Contoh: PAI" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                        <div v-if="form.errors.nama_mapel" class="text-xs text-danger-500 mt-1">{{ form.errors.nama_mapel }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelompok</label>
                            <select v-model="form.kelompok" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all appearance-none cursor-pointer">
                                <option value="A">Kelompok A</option>
                                <option value="B">Kelompok B</option>
                                <option value="C">Kelompok C</option>
                                <option value="Muatan Lokal">Muatan Lokal</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5 block">Urutan</label>
                            <input v-model="form.urutan" type="number" min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-medium focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all" />
                            <div v-if="form.errors.urutan" class="text-xs text-danger-500 mt-1">{{ form.errors.urutan }}</div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100 mt-5">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-danger-50 flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-danger-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi Hapus</h2>
                <p class="text-sm text-slate-500 mb-6">Yakin hapus mapel ini? Data nilai terkait ikut terhapus.</p>
                <div class="flex gap-3">
                    <button @click="showDeleteModal = false" class="flex-1 px-4 py-3 bg-white border border-slate-200 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition-all">Batal</button>
                    <button @click="executeDelete" class="flex-1 px-4 py-3 bg-danger-500 text-white font-medium rounded-xl hover:bg-danger-600 transition-all">Hapus</button>
                </div>
            </div>
        </Modal>

        <!-- Success Modal -->
        <Modal :show="showSuccessModal" @close="showSuccessModal = false" maxWidth="sm">
            <div class="p-6 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-success-50 flex items-center justify-center mb-4 shadow-inner">
                    <svg class="h-8 w-8 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-2">Berhasil!</h2>
                <p class="text-sm text-slate-500 mb-5">Data mapel berhasil disimpan.</p>
                <button @click="showSuccessModal = false" class="px-8 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all">Tutup</button>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
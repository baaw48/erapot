<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    ekskuls: Array,
});

const isModalOpen = ref(false);
const modalMode = ref('add'); // 'add' or 'edit'
const editId = ref(null);

const form = useForm({
    nama_ekskul: '',
    keterangan: '',
    is_active: true,
});

const openAddModal = () => {
    modalMode.value = 'add';
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (ekskul) => {
    modalMode.value = 'edit';
    editId.value = ekskul.id;
    form.nama_ekskul = ekskul.nama_ekskul;
    form.keterangan = ekskul.keterangan;
    form.is_active = ekskul.is_active;
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = () => {
    if (modalMode.value === 'add') {
        form.post(route('ekskul.store'), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.put(route('ekskul.update', editId.value), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteEkskul = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus ekskul ini?')) {
        router.delete(route('ekskul.destroy', id));
    }
};

const toggleStatus = (ekskul) => {
    router.put(route('ekskul.update', ekskul.id), {
        nama_ekskul: ekskul.nama_ekskul,
        keterangan: ekskul.keterangan,
        is_active: !ekskul.is_active,
    }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Master Data Ekstrakurikuler" />
    <AuthenticatedLayout>
        <PageHeader 
            title="Master Data Ekstrakurikuler"
            description="Kelola daftar ekstrakurikuler yang dapat dipilih oleh wali kelas."
            icon="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
        />

        <div class="animate-fade-in space-y-6">
            <div v-if="$page.props.flash?.success" class="flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-5 py-4 rounded-2xl shadow-sm">
                <span class="font-bold text-sm">{{ $page.props.flash.success }}</span>
            </div>

            <div class="flex justify-end">
                <button @click="openAddModal" class="px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Ekskul
                </button>
            </div>

            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-slate-200/50 dark:border-slate-700/50 rounded-2xl sm:rounded-[2rem] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Nama Ekstrakurikuler</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50">
                            <tr v-for="(ekskul, index) in ekskuls" :key="ekskul.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4 text-center text-xs font-bold text-slate-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-sm text-slate-700 dark:text-white">{{ ekskul.nama_ekskul }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ ekskul.keterangan || '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button @click="toggleStatus(ekskul)" :class="ekskul.is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400'" class="px-3 py-1 text-[10px] font-black uppercase rounded-full transition-colors">
                                        {{ ekskul.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openEditModal(ekskul)" class="p-2 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="deleteEkskul(ekskul.id)" class="p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!ekskuls || ekskuls.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data ekstrakurikuler.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="isModalOpen" @close="closeModal" maxWidth="md">
            <div class="p-6 dark:bg-slate-800">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">
                    {{ modalMode === 'add' ? 'Tambah Ekstrakurikuler' : 'Edit Ekstrakurikuler' }}
                </h2>
                
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Ekstrakurikuler <span class="text-rose-500">*</span></label>
                        <TextInput v-model="form.nama_ekskul" type="text" class="w-full" required placeholder="Contoh: Pramuka" />
                        <InputError :message="form.errors.nama_ekskul" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Keterangan (Opsional)</label>
                        <TextInput v-model="form.keterangan" type="text" class="w-full" placeholder="Deskripsi singkat..." />
                        <InputError :message="form.errors.keterangan" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" v-model="form.is_active" id="is_active" class="rounded border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" />
                        <label for="is_active" class="text-sm font-semibold text-slate-700 dark:text-slate-300">Aktif (Muncul di dropdown)</label>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition-colors disabled:opacity-70">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeInAnim 0.15s ease-out forwards;
}
@keyframes fadeInAnim {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

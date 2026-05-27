<script setup>
import { ref, watch, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    kelas: Object,
    gurus: Array,
    filters: Object,
});

const showModal = ref(false);
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const itemToDelete = ref(null);
const isEditing = ref(false);
const editingId = ref(null);

const perPage = ref(props.filters?.per_page || 10);
watch(perPage, (newPerPage) => {
    router.get(route('kelas.index'), { per_page: newPerPage }, { preserveState: true, preserveScroll: true });
});

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

const closeModal = () => {
    showModal.value = false;
};

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

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    itemToDelete.value = null;
};
</script>

<template>
    <Head title="Data Master Kelas" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl text-slate-800 leading-tight tracking-tight">Data Kelas</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kelola Master Data Kelas</p>
                </div>
            </div>
        </template>
        
        <div class="animate-fade-in space-y-6 max-w-7xl mx-auto pb-12">
            
            <div v-if="$page.props.flash && $page.props.flash.success" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl shadow-sm" role="alert">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-bold text-sm">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Tabel Data -->
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="px-8 py-6 border-b border-slate-100 bg-white/50 backdrop-blur-sm relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-black text-slate-800">Daftar Rombongan Belajar (Rombel)</h3>
                        <p class="text-sm font-semibold text-slate-500 mt-1">Total ada <span class="font-bold text-brand-600">{{ kelas.total }}</span> kelas terdaftar.</p>
                    </div>
                    <button @click="openAddModal" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Kelas
                    </button>
                </div>

                <!-- Header Tabel -->
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-black text-sm">
                            {{ kelas.total }}
                        </div>
                        <span class="text-sm font-bold text-slate-600">Total Kelas Terdaftar</span>
                    </div>
                    
                    <div class="w-full sm:w-32 shrink-0">
                        <div class="relative">
                            <select id="perPage" v-model="perPage" class="w-full bg-white border border-slate-200 text-slate-700 rounded-xl px-4 py-2 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-sm">
                                <option value="10">10 Baris</option>
                                <option value="25">25 Baris</option>
                                <option value="50">50 Baris</option>
                                <option value="100">100 Baris</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-4 w-16 text-center">No.</th>
                                <th class="px-8 py-4">Nama Kelas</th>
                                <th class="px-8 py-4 text-center">Tingkat</th>
                                <th class="px-8 py-4">Wali Kelas</th>
                                <th class="px-8 py-4 text-center">KKTP</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-for="(k, index) in kelas.data" :key="k.id" class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-5 text-center text-sm font-bold text-slate-400">
                                    {{ (kelas.current_page - 1) * kelas.per_page + index + 1 }}
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center font-black shadow-inner">
                                            {{ k.nama_kelas.charAt(0) }}
                                        </div>
                                        <div class="text-sm font-bold text-slate-800">{{ k.nama_kelas }}</div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-black border border-slate-200">
                                        {{ k.tingkat }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black uppercase shrink-0 border border-white shadow-sm">
                                            {{ k.wali_kelas ? k.wali_kelas.name.charAt(0) : '?' }}
                                        </div>
                                        <span class="text-sm font-bold text-slate-700">{{ k.wali_kelas ? k.wali_kelas.name : '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-sm font-black text-brand-600 bg-brand-50 px-2 py-1 rounded-lg border border-brand-100">{{ k.kktp }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditModal(k)" class="p-2.5 text-brand-500 hover:bg-brand-50 rounded-xl transition-colors" title="Edit Kelas">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(k.id)" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors" title="Hapus Kelas">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="kelas.data.length === 0">
                                <td colspan="6" class="px-8 py-16 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-600">Belum Ada Kelas</h3>
                                    <p class="text-xs font-medium text-slate-400 mt-1">Silakan tambahkan data rombel/kelas baru.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Paginasi -->
                <div class="px-8 py-4 border-t border-slate-100 bg-slate-50" v-if="kelas.links && kelas.links.length > 3">
                    <Pagination :links="kelas.links" />
                </div>
            </div>
        </div>
        
        <!-- Form Modal (Tambah/Edit) -->
        <Modal :show="showModal" @close="closeModal" maxWidth="md">
            <div class="p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                <div class="flex items-center gap-4 mb-8 relative z-10">
                    <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg v-if="isEditing" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800">{{ isEditing ? 'Edit Data Kelas' : 'Tambah Kelas Baru' }}</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5">Lengkapi formulir di bawah</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6 relative z-10">
                    <div class="space-y-2">
                        <label for="nama_kelas" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama Kelas</label>
                        <input id="nama_kelas" v-model="form.nama_kelas" type="text" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="Contoh: VII-A, VIII-1, dll" />
                        <div v-if="form.errors.nama_kelas" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.nama_kelas }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label for="tingkat" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Tingkat</label>
                            <select id="tingkat" v-model="form.tingkat" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-2xl px-4 py-3 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer" required>
                                <option value="7">Kelas 7</option>
                                <option value="8">Kelas 8</option>
                                <option value="9">Kelas 9</option>
                            </select>
                            <div v-if="form.errors.tingkat" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.tingkat }}</div>
                        </div>
                        <div class="space-y-2">
                            <label for="kktp" class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">KKTP</label>
                            <input id="kktp" v-model="form.kktp" type="number" min="0" max="100" class="w-full bg-slate-50 border border-slate-200 text-brand-700 rounded-2xl px-4 py-3 font-black focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all" required placeholder="75" />
                            <div v-if="form.errors.kktp" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.kktp }}</div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest">Wali Kelas</label>
                        <SearchableSelect
                            v-model="form.wali_kelas_id"
                            :options="guruOptions"
                            placeholder="-- Kosong / Belum Ada --"
                            searchPlaceholder="Ketik nama guru..."
                        />
                        <div v-if="form.errors.wali_kelas_id" class="text-rose-500 text-xs font-bold mt-1">{{ form.errors.wali_kelas_id }}</div>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-sm font-bold rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-3 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center justify-center gap-2">
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
                    Apakah Anda yakin ingin menghapus kelas ini? <strong class="text-rose-600">Seluruh data siswa dan nilai di rombel ini akan ikut terhapus permanen!</strong>
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
                <p class="text-sm font-semibold text-slate-500 mb-8 relative z-10">Data kelas telah berhasil disimpan.</p>
                <div class="flex justify-center relative z-10">
                    <button @click="showSuccessModal = false" class="px-8 py-3 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Tutup
                    </button>
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

/* Hilangkan panah spinner di input number */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type=number] { -moz-appearance: textfield; }
</style>

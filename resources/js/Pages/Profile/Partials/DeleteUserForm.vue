<script setup>
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <section class="space-y-6">
        <header class="mb-8 flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black dark:text-white tracking-tight">Hapus Akun</h2>
                <p class="text-xs font-bold dark:text-slate-400 uppercase tracking-widest mt-0.5">Tindakan ini bersifat permanen</p>
            </div>
        </header>

        <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-5 mb-6">
            <p class="text-sm font-semibold text-rose-700 leading-relaxed">
                Setelah akun Anda dihapus, semua data dan sumber daya yang terkait akan <strong class="font-black">dihapus secara permanen</strong>. Sebelum menghapus akun, mohon unduh data apa pun yang ingin Anda pertahankan.
            </p>
        </div>

        <button @click="confirmUserDeletion" class="px-6 py-3.5 bg-rose-600 text-white text-sm font-bold rounded-xl shadow-md hover:bg-rose-700 hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Hapus Akun Secara Permanen
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal" maxWidth="sm">
            <div class="p-8 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-rose-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 mb-6 relative z-10">
                    <svg class="h-10 w-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                
                <h2 class="text-xl font-black dark:text-white mb-2 relative z-10">Konfirmasi Penghapusan</h2>
                
                <p class="text-sm font-semibold dark:text-slate-400 mb-6 relative z-10">
                    Apakah Anda yakin ingin menghapus akun ini? Silakan masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun ini <strong class="text-rose-600">secara permanen</strong>.
                </p>

                <div class="mt-6 text-left relative z-10">
                    <label for="password" class="sr-only">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 dark:text-slate-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="w-full bg-slate-50 dark:bg-slate-800/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 dark:text-white rounded-2xl pl-11 pr-4 py-3.5 font-bold focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all placeholder:font-normal placeholder:dark:text-slate-400"
                            placeholder="Masukkan password Anda..."
                            @keyup.enter="deleteUser"
                        />
                    </div>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-8 flex gap-3 relative z-10">
                    <button @click="closeModal" class="flex-1 px-4 py-3 bg-white border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:bg-slate-800/50 text-slate-700 text-sm font-bold rounded-xl transition-all">
                        Batal
                    </button>

                    <button
                        class="flex-1 px-4 py-3 bg-rose-600 text-white text-sm font-bold rounded-xl shadow-md hover:bg-rose-700 hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center justify-center gap-2"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ form.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    username: user.username || '',
});
</script>

<template>
    <section>
        <header class="mb-8 flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black dark:text-white tracking-tight">Informasi Pribadi</h2>
                <p class="text-xs font-bold dark:text-slate-400 uppercase tracking-widest mt-0.5">Perbarui nama dan username Anda</p>
            </div>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-6">
            <div class="space-y-2">
                <label for="name" class="flex items-center gap-2 text-xs font-black dark:text-slate-400 uppercase tracking-widest pl-1">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 dark:text-white rounded-2xl px-4 py-3.5 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:dark:text-slate-400"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div class="space-y-2">
                <label for="username" class="flex items-center gap-2 text-xs font-black dark:text-slate-400 uppercase tracking-widest pl-1">Username Login</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 dark:text-slate-400 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input
                        id="username"
                        type="text"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 dark:text-white rounded-2xl pl-11 pr-4 py-3.5 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:dark:text-slate-400"
                        v-model="form.username"
                        required
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.username" />
            </div>

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                <button type="submit" :disabled="form.processing" class="px-6 py-3 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 flex items-center gap-2">
                    <svg v-if="form.processing" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>

                <Transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0 translate-x-2" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0 -translate-x-2">
                    <p v-if="form.recentlySuccessful" class="text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 flex items-center gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Tersimpan
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

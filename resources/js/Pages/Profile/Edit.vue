<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-brand-50 text-brand-600 rounded-xl">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-black text-xl dark:text-white leading-tight tracking-tight">Pengaturan Profil</h2>
                    <p class="text-xs font-bold dark:text-slate-400 uppercase tracking-widest mt-0.5">Kelola Akun dan Keamanan</p>
                </div>
            </div>
        </template>

        <div class="py-12 animate-fade-in">
            <div class="mx-auto max-w-4xl space-y-8 sm:px-6 lg:px-8 pb-12">
                
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.05s; animation-fill-mode: both;">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                    <div class="p-8 sm:p-10 relative z-10">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.2s; animation-fill-mode: both;">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-100 dark:bg-indigo-900/30 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                    <div class="p-8 sm:p-10 relative z-10">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-[0_4px_24px_rgba(0,0,0,0.02)] relative animate-slide-up" style="animation-delay: 0.3s; animation-fill-mode: both;">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-rose-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                    <div class="p-8 sm:p-10 relative z-10">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in { animation: fadeInAnim 0.15s ease-out forwards; }
.animate-slide-up { animation: slideUp 0.15s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
@keyframes fadeInAnim { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
@keyframes slideUp { 0% { opacity: 0; transform: translateY(24px); } 100% { opacity: 1; transform: translateY(0); } }
</style>

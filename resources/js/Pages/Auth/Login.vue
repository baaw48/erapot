<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: String,
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk" />

    <div class="min-h-screen flex items-center justify-center bg-slate-100 dark:bg-slate-900 px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl shadow-lg shadow-primary-500/30 mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0 -3.332.477 -4.5 1.253" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">Selamat Datang</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Masuk ke Sistem E-Rapot</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 p-8">
                <!-- Status Message -->
                <div v-if="status" class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-center">
                    <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ status }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                id="username"
                                type="text"
                                v-model="form.username"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan username"
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder-slate-400 dark:placeholder-slate-500"
                            />
                        </div>
                        <p v-if="form.errors.username" class="mt-1 text-sm text-red-500">{{ form.errors.username }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full pl-11 pr-12 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all placeholder-slate-400 dark:placeholder-slate-500"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-slate-500 hover:text-primary-500"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0 3 3 0 01-3.029m5.858.908a3 3 0 014.243 4.243 0 11-4.242-4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29-3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943 9.543-7a9.97 9.97 0 011.563-3.029" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 014.242 4.243 0 11-4.242-4.243M9.88 9.88l-3.29-3.29m7.532 7.532l3.29-3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943 9.543-7a10.025 10.025 0 01-4.132 5.411" />
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="w-4 h-4 text-primary-600 bg-slate-50 dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-primary-500"
                        />
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 dark:text-slate-300 cursor-pointer">Ingat saya</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647" />
                        </svg>
                        <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
                    </button>
                </form>
            </div>

            <p class="text-center mt-6 text-xs font-medium text-slate-400 dark:text-slate-500">
                &copy; {{ new Date().getFullYear() }} E-RAPOT. All rights reserved.
            </p>
        </div>
    </div>
</template>

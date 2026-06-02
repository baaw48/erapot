<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    status: String,
    canResetPassword: Boolean,
});

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const isDark = ref(false);
const isPasswordFocused = ref(false);

const toggleDark = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else if (savedTheme === 'light') {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - E-Rapor ASTS" />

    <!-- Main Container -->
    <div :style="{ backgroundColor: isDark ? '#0f172a' : '#f1f5f9' }" class="min-h-screen flex items-center justify-center p-6">

        <!-- Dark Mode Toggle -->
        <button
            @click="toggleDark"
            class="fixed top-6 right-6 z-50 w-12 h-12 rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-110"
            :style="{ backgroundColor: isDark ? '#fbbf24' : '#1e293b' }"
        >
            <svg v-if="!isDark" class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
            </svg>
            <svg v-else class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"/>
            </svg>
        </button>

        <!-- Login Card -->
        <div class="w-full max-w-md">

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6" style="background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 10px 40px rgba(59, 130, 246, 0.4);">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-2" :style="{ color: isDark ? '#ffffff' : '#1e293b' }">
                    Selamat Datang
                </h1>
                <p class="text-base" :style="{ color: isDark ? '#94a3b8' : '#64748b' }">
                    Masuk ke Sistem E-Rapor ASTS
                </p>
            </div>

            <!-- Form Card -->
            <div class="rounded-3xl p-8"
                :style="{
                    backgroundColor: isDark ? '#1e293b' : '#ffffff',
                    boxShadow: isDark ? '0 25px 50px -12px rgba(0,0,0,0.5)' : '0 25px 50px -12px rgba(0,0,0,0.15)'
                }">

                <!-- Success Message -->
                <div v-if="status" class="mb-6 p-4 rounded-xl text-center"
                    :style="{ backgroundColor: isDark ? 'rgba(34,197,94,0.1)' : '#f0fdf4', borderColor: isDark ? '#22c55e' : '#bbf7d0' }">
                    <p class="text-sm font-medium" :style="{ color: isDark ? '#4ade80' : '#16a34a' }">{{ status }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-semibold mb-2 uppercase tracking-wider"
                            :style="{ color: isDark ? '#94a3b8' : '#475569' }">
                            Username
                        </label>
                        <div class="relative">
                            <input
                                id="username"
                                type="text"
                                v-model="form.username"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan username"
                                class="w-full px-4 py-3.5 rounded-xl text-base transition-all outline-none"
                                :style="{
                                    backgroundColor: isDark ? '#0f172a' : '#f8fafc',
                                    border: form.errors.username ? '2px solid #ef4444' : '2px solid ' + (isDark ? '#334155' : '#e2e8f0'),
                                    color: isDark ? '#ffffff' : '#1e293b',
                                }"
                                @focus="(e) => e.target.style.borderColor = '#3b82f6'"
                                @blur="(e) => e.target.style.borderColor = form.errors.username ? '#ef4444' : (isDark ? '#334155' : '#e2e8f0')"
                            />
                        </div>
                        <p v-if="form.errors.username" class="mt-2 text-sm" style="color: #ef4444;">
                            {{ form.errors.username }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold mb-2 uppercase tracking-wider"
                            :style="{ color: isDark ? '#94a3b8' : '#475569' }">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full px-4 py-3.5 pr-12 rounded-xl text-base transition-all outline-none"
                                :style="{
                                    backgroundColor: isDark ? '#0f172a' : '#f8fafc',
                                    border: form.errors.password ? '2px solid #ef4444' : '2px solid ' + (isDark ? '#334155' : '#e2e8f0'),
                                    color: isDark ? '#ffffff' : '#1e293b',
                                }"
                                @focus="(e) => e.target.style.borderColor = '#3b82f6'"
                                @blur="(e) => e.target.style.borderColor = form.errors.password ? '#ef4444' : (isDark ? '#334155' : '#e2e8f0')"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 transition-colors"
                                :style="{ color: isDark ? '#64748b' : '#94a3b8' }"
                                @mouseenter="(e) => e.target.style.color = '#3b82f6'"
                                @mouseleave="(e) => e.target.style.color = isDark ? '#64748b' : '#94a3b8'"
                            >
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm" style="color: #ef4444;">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded border-2 mr-2 cursor-pointer"
                                :style="{
                                    backgroundColor: form.remember ? '#3b82f6' : 'transparent',
                                    borderColor: form.remember ? '#3b82f6' : (isDark ? '#475569' : '#cbd5e1')
                                }"
                            />
                            <span class="text-sm" :style="{ color: isDark ? '#94a3b8' : '#64748b' }">Ingat saya</span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-medium transition-colors hover:underline"
                            style="color: #3b82f6;">
                            Lupa password?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-4 rounded-xl text-white font-bold text-base transition-all flex items-center justify-center gap-2"
                        :style="{
                            backgroundColor: form.processing ? '#60a5fa' : '#3b82f6',
                            boxShadow: form.processing ? 'none' : '0 10px 15px -3px rgba(59, 130, 246, 0.4)'
                        }"
                        @mouseenter="(e) => !form.processing && (e.target.style.backgroundColor = '#2563eb')"
                        @mouseleave="(e) => e.target.style.backgroundColor = form.processing ? '#60a5fa' : '#3b82f6'"
                    >
                        <svg v-if="form.processing" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                        <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center mt-8 text-sm" :style="{ color: isDark ? '#64748b' : '#94a3b8' }">
                &copy; {{ new Date().getFullYear() }} E-Rapor ASTS
            </p>
        </div>
    </div>
</template>
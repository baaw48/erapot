<script setup>
import { ref, onMounted } from 'vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const isDark = ref(false);
const focusedField = ref('');
const isLoaded = ref(false);

// Get sekolah data from page props
const sekolah = page.props.sekolah || null;
const namaSekolah = sekolah?.nama_sekolah || 'E-Rapor ASTS';
const logoUrl = sekolah?.logo_url || null;

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
    setTimeout(() => { isLoaded.value = true; }, 100);
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
    <div class="min-h-screen flex" :style="{ backgroundColor: isDark ? '#030712' : '#fafbfc' }">

        <!-- Left Panel - Branding with School Logo -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center relative overflow-hidden"
            :style="{ backgroundColor: isDark ? '#0f172a' : '#0f172a' }">

            <!-- Animated Gradient Background -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full"
                    style="background: linear-gradient(135deg, #1e3a8a 0%, #312e81 50%, #1e1b4b 100%);">
                </div>
                <!-- Decorative Circles -->
                <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #3b82f6, transparent); filter: blur(80px);">
                </div>
                <div class="absolute bottom-1/4 right-1/4 w-80 h-80 rounded-full opacity-20"
                    style="background: radial-gradient(circle, #8b5cf6, transparent); filter: blur(80px);">
                </div>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-center px-12" style="opacity: 0; transform: translateY(20px);"
                :style="isLoaded ? { opacity: '1', transform: 'translateY(0)', transition: 'all 0.8s ease-out' } : {}">

                <!-- School Logo -->
                <div class="mb-10">
                    <div v-if="logoUrl" class="mb-8">
                        <img :src="logoUrl" alt="Logo Sekolah" class="w-32 h-32 mx-auto object-contain rounded-2xl"
                            style="filter: drop-shadow(0 20px 40px rgba(255,255,255,0.2));">
                    </div>
                    <div v-else class="w-28 h-28 mx-auto rounded-2xl flex items-center justify-center mb-8"
                        style="background: linear-gradient(135deg, rgba(59,130,246,0.9), rgba(99,102,241,0.9)); box-shadow: 0 20px 40px rgba(59,130,246,0.3);">
                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>

                    <!-- School Name -->
                    <h1 class="text-5xl font-black text-white mb-4 tracking-tight">
                        {{ namaSekolah }}
                    </h1>

                    <!-- Tagline -->
                    <p class="text-xl text-slate-300 max-w-md mx-auto leading-relaxed">
                        Sistem Informasi Raport Digital
                    </p>
                </div>

                <!-- Decorative Element -->
                <div class="mt-16">
                    <div class="w-24 h-1 mx-auto rounded-full"
                        style="background: linear-gradient(90deg, #3b82f6, #8b5cf6);">
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-16 grid grid-cols-3 gap-8">
                    <div class="text-center">
                        <p class="text-4xl font-bold text-white">100%</p>
                        <p class="text-sm text-slate-400 mt-1">Digital</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-bold text-white">24/7</p>
                        <p class="text-sm text-slate-400 mt-1">Akses</p>
                    </div>
                    <div class="text-center">
                        <p class="text-4xl font-bold text-white">Secure</p>
                        <p class="text-sm text-slate-400 mt-1">Data</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 relative">

            <!-- Dark Mode Toggle -->
            <button
                @click="toggleDark"
                class="fixed top-8 right-8 z-50 w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                :style="{
                    backgroundColor: isDark ? '#fbbf24' : '#f1f5f9',
                    boxShadow: isDark ? '0 4px 20px rgba(251,191,36,0.4)' : '0 4px 20px rgba(0,0,0,0.1)'
                }"
            >
                <svg v-if="!isDark" class="w-5 h-5" :style="{ color: '#1e293b' }" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                </svg>
                <svg v-else class="w-5 h-5" :style="{ color: '#000000' }" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z"/>
                </svg>
            </button>

            <!-- Login Card -->
            <div class="w-full max-w-md" style="opacity: 0; transform: translateY(30px);"
                :style="isLoaded ? { opacity: '1', transform: 'translateY(0)', transition: 'all 0.6s ease-out 0.2s' } : {}">

                <!-- Mobile Logo with School Name -->
                <div class="lg:hidden text-center mb-10">
                    <div v-if="logoUrl" class="mb-5">
                        <img :src="logoUrl" alt="Logo Sekolah" class="w-20 h-20 mx-auto object-contain rounded-xl">
                    </div>
                    <div v-else class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center mb-5"
                        style="background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 15px 30px rgba(59,130,246,0.3);">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-1">{{ namaSekolah }}</h1>
                    <p class="text-base" style="color: #60a5fa;">Sistem Raport Digital</p>
                </div>

                <!-- Header -->
                <div class="mb-10">
                    <h2 class="text-4xl font-bold mb-3" :style="{ color: isDark ? '#f9fafb' : '#0f172a' }">
                        Selamat Datang
                    </h2>
                    <p class="text-base" :style="{ color: isDark ? '#6b7280' : '#6b7280' }">
                        Silakan masukkan kredensial akun Anda
                    </p>
                </div>

                <!-- Success Message -->
                <div v-if="status" class="mb-8 p-5 rounded-2xl border"
                    :style="{
                        backgroundColor: isDark ? 'rgba(34,197,94,0.08)' : '#f0fdf4',
                        borderColor: isDark ? 'rgba(34,197,94,0.3)' : '#bbf7d0'
                    }">
                    <p class="text-sm font-medium" :style="{ color: isDark ? '#4ade80' : '#16a34a' }">
                        {{ status }}
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-7">

                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-semibold mb-3"
                            :style="{ color: isDark ? '#9ca3af' : '#374151' }">
                            Username
                        </label>
                        <input
                            id="username"
                            type="text"
                            v-model="form.username"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Ketik username Anda"
                            class="w-full px-5 py-4 rounded-xl text-base transition-all duration-200 outline-none"
                            :style="{
                                backgroundColor: isDark ? '#111827' : '#ffffff',
                                border: '2px solid ' + (form.errors.username ? '#f87171' : (focusedField === 'username' ? '#3b82f6' : (isDark ? '#1f2937' : '#e5e7eb'))),
                                color: isDark ? '#ffffff' : '#111827',
                                boxShadow: focusedField === 'username' ? (isDark ? '0 0 0 4px rgba(59,130,246,0.1)' : '0 0 0 4px rgba(59,130,246,0.1)') : 'none'
                            }"
                            @focus="focusedField = 'username'"
                            @blur="focusedField = ''"
                        />
                        <p v-if="form.errors.username" class="mt-2 text-sm font-medium" style="color: #f87171;">
                            {{ form.errors.username }}
                        </p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold mb-3"
                            :style="{ color: isDark ? '#9ca3af' : '#374151' }">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Ketik password Anda"
                                class="w-full px-5 py-4 pr-14 rounded-xl text-base transition-all duration-200 outline-none"
                                :style="{
                                    backgroundColor: isDark ? '#111827' : '#ffffff',
                                    border: '2px solid ' + (form.errors.password ? '#f87171' : (focusedField === 'password' ? '#3b82f6' : (isDark ? '#1f2937' : '#e5e7eb'))),
                                    color: isDark ? '#ffffff' : '#111827',
                                    boxShadow: focusedField === 'password' ? (isDark ? '0 0 0 4px rgba(59,130,246,0.1)' : '0 0 0 4px rgba(59,130,246,0.1)') : 'none'
                                }"
                                @focus="focusedField = 'password'"
                                @blur="focusedField = ''"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-5 top-1/2 -translate-y-1/2 transition-all duration-200"
                                :style="{ color: isDark ? '#6b7280' : '#9ca3af' }"
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
                        <p v-if="form.errors.password" class="mt-2 text-sm font-medium" style="color: #f87171;">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-5 h-5 rounded cursor-pointer transition-all duration-200"
                                :style="{
                                    accentColor: '#3b82f6',
                                    backgroundColor: form.remember ? '#3b82f6' : 'transparent',
                                    border: '2px solid ' + (form.remember ? '#3b82f6' : (isDark ? '#4b5563' : '#d1d5db'))
                                }"
                            />
                            <span class="ml-3 text-sm" :style="{ color: isDark ? '#9ca3af' : '#6b7280' }">
                                Ingat saya
                            </span>
                        </label>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm font-semibold transition-all duration-200 hover:underline"
                            style="color: #3b82f6;">
                            Lupa password?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-4 rounded-xl text-white font-semibold text-base transition-all duration-300 relative overflow-hidden"
                        :style="{
                            backgroundColor: form.processing ? '#60a5fa' : '#3b82f6',
                        }"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                            Memproses...
                        </span>
                        <span v-else class="relative z-10">Masuk ke Dashboard</span>
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-12 text-center text-sm" :style="{ color: isDark ? '#4b5563' : '#9ca3af' }">
                    &copy; {{ new Date().getFullYear() }} {{ namaSekolah }}
                </p>
            </div>
        </div>
    </div>
</template>
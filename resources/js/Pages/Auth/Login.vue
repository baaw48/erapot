<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
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
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden selection:bg-brand-500 selection:text-white">
        
        <!-- Premium Animated Background -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Top Right Blob -->
            <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-br from-brand-400/20 to-purple-400/20 blur-[100px] animate-blob"></div>
            <!-- Bottom Left Blob -->
            <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-tr from-blue-400/20 to-brand-300/20 blur-[100px] animate-blob animation-delay-2000"></div>
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgwLCAwLCAwLCAwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-50"></div>
        </div>

        <div class="w-full max-w-[28rem] px-6 relative z-10 animate-fade-in-up">
            
            <!-- Card Container -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white p-10 sm:p-12 relative overflow-hidden">
                
                <!-- Inner Glow -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-32 bg-brand-400/10 blur-[50px] rounded-full pointer-events-none"></div>

                <div class="text-center mb-10 relative">
                    <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url" class="mx-auto w-24 h-24 mb-6 relative">
                        <div class="absolute inset-0 bg-brand-100 rounded-full blur-xl opacity-50"></div>
                        <img :src="$page.props.sekolah.logo_url" alt="Logo Sekolah" class="w-full h-full object-contain relative z-10" />
                    </div>
                    <div v-else class="mx-auto w-16 h-16 bg-gradient-to-tr from-brand-600 to-indigo-500 rounded-2xl flex items-center justify-center shadow-lg shadow-brand-500/30 mb-6 transform rotate-3 hover:rotate-6 transition-transform">
                        <svg class="w-8 h-8 text-white -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Selamat Datang</h2>
                    <p class="text-sm font-semibold text-slate-500">Masuk untuk mengelola nilai E-Rapot</p>
                </div>

                <div v-if="status" class="mb-6 text-sm font-bold text-emerald-600 text-center bg-emerald-50 py-3 px-4 rounded-xl border border-emerald-100">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label for="username" class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-widest pl-1">Username</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input
                                id="username"
                                type="text"
                                class="block w-full bg-slate-50/50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-4 py-3.5 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400"
                                v-model="form.username"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan username"
                            />
                        </div>
                        <InputError class="mt-1" :message="form.errors.username" />
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="flex items-center justify-between text-xs font-black text-slate-500 uppercase tracking-widest pl-1">
                            <span>Password</span>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="text-[10px] text-brand-600 hover:text-brand-700 normal-case tracking-normal">
                                Lupa sandi?
                            </Link>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-brand-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full bg-slate-50/50 border border-slate-200 text-slate-800 rounded-2xl pl-11 pr-12 py-3.5 font-bold focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all placeholder:font-normal placeholder:text-slate-400"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-brand-500 focus:outline-none transition-colors"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 mr-3">
                                <input type="checkbox" name="remember" v-model="form.remember" class="peer appearance-none w-5 h-5 border-2 border-slate-300 rounded-md checked:bg-brand-600 checked:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-500/20 transition-all cursor-pointer">
                                <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-600 group-hover:text-slate-800 transition-colors">Ingat sesi saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white text-sm font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-0.5 transition-all disabled:opacity-70 disabled:hover:translate-y-0"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>{{ form.processing ? 'Memproses...' : 'Masuk ke Aplikasi' }}</span>
                            <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
            
            <p class="text-center mt-8 text-xs font-bold text-slate-400">
                &copy; {{ new Date().getFullYear() }} Sistem Informasi E-Rapot
            </p>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-blob {
    animation: blob 10s infinite alternate;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
</style>

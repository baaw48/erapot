<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Selamat Datang - E-Rapor ASTS" />
    
    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 relative overflow-hidden selection:bg-brand-500 selection:text-white">
        
        <!-- Premium Animated Background -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Top Right Blob -->
            <div class="absolute -top-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-br from-brand-400/20 to-purple-400/20 blur-[100px] animate-blob"></div>
            <!-- Bottom Left Blob -->
            <div class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-tr from-blue-400/20 to-brand-300/20 blur-[100px] animate-blob animation-delay-2000"></div>
            <!-- Center Blob -->
            <div class="absolute top-[20%] left-[20%] w-[40%] h-[40%] rounded-full bg-gradient-to-tr from-pink-400/10 to-indigo-300/10 blur-[100px] animate-blob animation-delay-4000"></div>
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgwLCAwLCAwLCAwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-50"></div>
        </div>

        <div class="w-full max-w-4xl px-6 relative z-10 animate-fade-in-up">
            
            <!-- Main Content Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[3rem] shadow-[0_8px_40px_rgb(0,0,0,0.04)] border border-white p-12 md:p-20 relative overflow-hidden text-center group">
                
                <!-- Inner Glow -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-32 bg-brand-400/10 blur-[50px] rounded-full pointer-events-none transition-opacity duration-700 group-hover:opacity-100 opacity-50"></div>

                <div class="mb-10 relative">
                    <div v-if="$page.props.sekolah && $page.props.sekolah.logo_url" class="mx-auto w-32 h-32 mb-8 relative group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-brand-200 rounded-full blur-2xl opacity-40 group-hover:opacity-60 transition-opacity"></div>
                        <img :src="$page.props.sekolah.logo_url" alt="Logo Sekolah" class="w-full h-full object-contain relative z-10 drop-shadow-xl" />
                    </div>
                    <div v-else class="mx-auto w-24 h-24 bg-gradient-to-tr from-brand-600 to-indigo-500 rounded-3xl flex items-center justify-center shadow-2xl shadow-brand-500/30 mb-8 transform rotate-3 group-hover:rotate-6 group-hover:scale-105 transition-all duration-500">
                        <svg class="w-12 h-12 text-white -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    
                    <h1 class="text-4xl md:text-6xl font-black text-slate-800 tracking-tight mb-6 leading-tight">
                        Sistem <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600">E-Rapor ASTS</span>
                    </h1>
                    
                    <p class="text-lg md:text-xl font-semibold text-slate-500 dark:text-slate-400 dark:text-slate-500 max-w-2xl mx-auto leading-relaxed">
                        Solusi modern, cepat, dan terintegrasi untuk pengisian serta pencetakan nilai Asesmen Sumatif Tengah Semester bagi Bapak/Ibu Guru.
                    </p>
                </div>

                <nav v-if="canLogin" class="flex flex-col sm:flex-row justify-center gap-4 pt-6">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-8 py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center justify-center gap-2"
                    >
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-8 py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white font-bold rounded-2xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:-translate-y-1 transition-all flex items-center justify-center gap-2"
                        >
                            <span>Login Aplikasi</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-8 py-4 bg-white border-2 border-slate-200 text-slate-700 dark:text-slate-200 font-bold rounded-2xl shadow-sm hover:border-brand-300 hover:bg-slate-50 hover:-translate-y-1 transition-all flex items-center justify-center"
                        >
                            Daftar Baru
                        </Link>
                    </template>
                </nav>
            </div>
            
            <p class="absolute -bottom-16 left-0 right-0 text-center text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                <span v-if="$page.props.sekolah && $page.props.sekolah.nama_sekolah">{{ $page.props.sekolah.nama_sekolah }}</span>
                <span v-else>E-RAPOT SISTEM</span> 
                &copy; {{ new Date().getFullYear() }}
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
        transform: translateY(30px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-blob {
    animation: blob 12s infinite alternate ease-in-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
    33% { transform: translate(40px, -60px) scale(1.1) rotate(5deg); }
    66% { transform: translate(-30px, 30px) scale(0.9) rotate(-5deg); }
    100% { transform: translate(0px, 0px) scale(1) rotate(0deg); }
}
</style>

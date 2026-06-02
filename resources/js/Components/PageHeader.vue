<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    icon: {
        type: String,
        default: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    }
});
</script>

<template>
    <div class="relative overflow-hidden rounded-[2rem] bg-slate-50 dark:bg-slate-900 shadow-xl group border border-slate-200 dark:border-transparent mb-6">
        <!-- Abstract Mesh Background -->
        <div class="absolute inset-0 opacity-40 dark:opacity-60">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-primary-400 dark:bg-primary-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob"></div>
            <div class="absolute top-0 -right-4 w-96 h-96 bg-indigo-400 dark:bg-indigo-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-24 left-32 w-96 h-96 bg-purple-400 dark:bg-purple-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[80px] animate-blob animation-delay-4000"></div>
        </div>
        
        <div class="relative z-10 p-6 md:p-8 flex items-center gap-6">
            <div class="hidden sm:flex p-4 bg-white/60 dark:bg-white/10 backdrop-blur-xl border border-white/40 dark:border-white/20 rounded-2xl shadow-[0_5px_15px_rgba(0,0,0,0.05)] dark:shadow-[0_0_20px_rgba(255,255,255,0.05)] transform group-hover:scale-105 transition-all duration-300">
                <svg class="w-8 h-8 text-primary-500 dark:text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="icon" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-1 tracking-tight">
                    {{ title }}
                </h2>
                <p v-if="description" class="text-slate-600 dark:text-slate-300 text-sm font-medium">
                    {{ description }}
                </p>
            </div>
            
            <div class="ml-auto relative z-10">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-blob {
    animation: blob 7s infinite alternate ease-in-out;
}
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}
</style>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap justify-center gap-1 mt-2 mb-2">
        <template v-for="(link, k) in links" :key="k">
            <div v-if="link.url === null" 
                 class="px-4 py-2 text-sm font-bold leading-4 text-slate-400 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center min-w-[40px]" 
                 v-html="link.label">
            </div>
            <Link v-else 
                  class="px-4 py-2 text-sm font-bold leading-4 rounded-xl transition-all duration-200 flex items-center justify-center min-w-[40px]"
                  :class="link.active 
                    ? 'bg-gradient-to-r from-brand-600 to-brand-500 text-white shadow-md shadow-brand-500/30' 
                    : 'bg-white text-slate-600 border border-slate-200 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200'"
                  :href="link.url" 
                  v-html="link.label">
            </Link>
        </template>
    </div>
</template>

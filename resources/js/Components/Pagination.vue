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
                 class="px-4 py-2 text-sm font-bold leading-4 text-slate-400 bg-slate-50 dark:bg-slate-800 dark:text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-center min-w-[40px]"
                 v-html="link.label">
            </div>
            <Link v-else
                  class="px-4 py-2 text-sm font-bold leading-4 rounded-lg transition-all duration-200 flex items-center justify-center min-w-[40px]"
                  :class="link.active
                    ? 'bg-blue-600 text-white shadow-md dark:shadow-blue-900/30'
                    : 'bg-white dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400'"
                  :href="link.url"
                  v-html="link.label">
            </Link>
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    isCollapsed: {
        type: Boolean,
        default: false,
    }
});

const classes = computed(() =>
    props.active
        ? 'nav-item active w-full flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-brand-600/40 to-brand-600/10 border-l-4 border-brand-400 shadow-[inset_0_0_20px_rgba(79,70,229,0.15)] transition-all duration-300'
        : 'nav-item w-full flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-slate-400 hover:bg-brand-500/15 hover:text-brand-200 transition-all duration-300 border-l-4 border-transparent'
);
</script>

<template>
    <Link :href="href" :class="classes" :title="isCollapsed ? $slots.default()[0]?.children : ''" style="gap: 12px;">
        <div class="shrink-0 transition-transform duration-300" :class="{'scale-110 text-white': active, 'group-hover:scale-110 group-hover:text-brand-300': !active}">
            <slot name="icon" />
        </div>
        
        <span 
            class="transition-all duration-300 whitespace-nowrap overflow-hidden"
            :class="{ 'opacity-0 w-0': isCollapsed, 'opacity-100 w-auto': !isCollapsed }"
            style="transition-property: opacity, width;"
        >
            <slot />
        </span>
    </Link>
</template>

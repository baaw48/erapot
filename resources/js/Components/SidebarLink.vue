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
        default: false,
    },
    isCollapsed: {
        type: Boolean,
        default: false,
    }
});

const classes = computed(() =>
    props.active
        ? 'nav-item active w-full flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-white bg-white/20 backdrop-blur-sm border border-white/30 shadow-lg transition-all duration-300'
        : 'nav-item w-full flex items-center px-4 py-3 rounded-xl text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm transition-all duration-300 border border-transparent'
);
</script>

<template>
    <Link :href="href" :class="classes" :title="isCollapsed ? $slots.default()[0]?.children : ''">
        <div class="shrink-0 transition-all duration-300" :class="{ 'scale-110': active }">
            <slot name="icon" />
        </div>

        <span
            v-if="!isCollapsed"
            class="transition-all duration-300 whitespace-nowrap overflow-hidden"
        >
            <slot />
        </span>
    </Link>
</template>
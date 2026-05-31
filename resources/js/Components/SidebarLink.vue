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
        ? 'nav-item active w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white bg-white/15 border border-white/20 transition-all duration-200'
        : 'nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-white/60 hover:text-white hover:bg-white/10 transition-all duration-200'
);
</script>

<template>
    <Link :href="href" :class="classes" :title="isCollapsed ? $slots.default()[0]?.children : ''">
        <div class="shrink-0 transition-all duration-200" :class="{ 'scale-110': active }">
            <slot name="icon" />
        </div>

        <span
            v-if="!isCollapsed"
            class="transition-all duration-200 whitespace-nowrap overflow-hidden"
        >
            <slot />
        </span>
    </Link>
</template>
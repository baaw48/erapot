<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-xs font-bold tracking-widest text-gray-700 shadow-sm transition-all duration-300 hover:bg-gray-50 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-25 active:scale-95"
    >
        <slot />
    </button>
</template>

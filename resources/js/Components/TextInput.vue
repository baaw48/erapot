<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-xl border-gray-200 shadow-sm transition-all duration-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/20"
        v-model="model"
        ref="input"
    />
</template>

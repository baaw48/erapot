<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';

const props = defineProps({
    modelValue: {
        default: null,
    },
    options: {
        type: Array,
        required: true,
        // options: [{ value: ..., label: ... }]
    },
    placeholder: {
        type: String,
        default: '-- Pilih --',
    },
    searchPlaceholder: {
        type: String,
        default: 'Ketik untuk mencari...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    class: {
        type: String,
        default: '',
    },
    error: {
        type: Boolean,
        default: false,
    },
    success: {
        type: Boolean,
        default: false,
    },
    warning: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const search = ref('');
const searchInput = ref(null);
const container = ref(null);

const selectedOption = computed(() => {
    return props.options.find(o => String(o.value) === String(props.modelValue)) || null;
});

const filteredOptions = computed(() => {
    if (!search.value) return props.options;
    return props.options.filter(o =>
        String(o.label).toLowerCase().includes(search.value.toLowerCase())
    );
});

const dropdownStyle = ref({});

const updatePosition = () => {
    if (!isOpen.value || !container.value) return;
    const rect = container.value.getBoundingClientRect();
    // Jika posisi bawah hampir mentok layar, buka ke atas
    const spaceBelow = window.innerHeight - rect.bottom;
    const dropdownHeight = 250; // Perkiraan max height
    
    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
        dropdownStyle.value = {
            top: `${rect.top - dropdownHeight - 4}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`,
        };
    } else {
        dropdownStyle.value = {
            top: `${rect.bottom + 4}px`,
            left: `${rect.left}px`,
            width: `${rect.width}px`,
        };
    }
};

const open = () => {
    if (props.disabled) return;
    isOpen.value = true;
    search.value = '';
    
    // update position right after opening
    nextTick(() => {
        updatePosition();
        if (searchInput.value) searchInput.value.focus();
    });
};

const close = () => {
    isOpen.value = false;
    search.value = '';
};

const select = (option) => {
    emit('update:modelValue', option.value);
    emit('change', option.value);
    close();
};

const dropdownContainer = ref(null);

// Close on click outside
const handleClickOutside = (e) => {
    if (container.value && container.value.contains(e.target)) return;
    if (dropdownContainer.value && dropdownContainer.value.contains(e.target)) return;
    
    close();
};

const handleScroll = () => {
    if (isOpen.value) {
        updatePosition();
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
    window.addEventListener('scroll', handleScroll, true);
    window.addEventListener('resize', handleScroll);
});
onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
    window.removeEventListener('scroll', handleScroll, true);
    window.removeEventListener('resize', handleScroll);
});
</script>

<template>
    <div ref="container" class="relative" :class="props.class">
        <!-- Trigger Button -->
        <button
            type="button"
            @click="open"
            :disabled="disabled"
            class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-xl text-sm text-left shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed border"
            :class="[
                isOpen ? 'border-blue-400 ring-2 ring-blue-400/20 bg-white dark:bg-slate-800' : 'dark:bg-slate-800 dark:border-slate-600 dark:text-slate-200',
                error && !isOpen ? 'border-red-300 dark:border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:border-red-400 focus:ring-red-400' : '',
                warning && !isOpen ? 'border-amber-300 dark:border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 hover:border-amber-400 focus:ring-amber-400' : '',
                success && !isOpen ? 'border-green-300 dark:border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:border-green-400 focus:ring-green-400' : '',
                !error && !warning && !success && !isOpen ? 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600 hover:border-blue-300 dark:hover:border-blue-500 focus:ring-blue-400' : ''
            ]"
        >
            <span :class="[
                selectedOption && !error && !warning && !success ? 'text-slate-800 dark:text-slate-200 font-medium' : '',
                !selectedOption && !error && !warning && !success ? 'text-slate-400 dark:text-slate-500 dark:text-slate-400' : '',
                error ? 'font-medium text-red-700 dark:text-red-300' : '',
                warning ? 'font-medium text-amber-700 dark:text-amber-300' : '',
                success ? 'font-medium text-green-700 dark:text-green-300' : ''
            ]">
                {{ selectedOption ? selectedOption.label : placeholder }}
            </span>
            <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200" 
                 :class="[
                     isOpen ? 'rotate-180 text-brand-500' : '',
                     error ? 'text-rose-400' : '',
                     warning ? 'text-amber-400' : '',
                     success ? 'text-emerald-500' : '',
                     !error && !warning && !success && !isOpen ? 'text-slate-400' : ''
                 ]" 
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 scale-100 translate-y-0"
                leave-to-class="opacity-0 scale-95 -translate-y-1"
            >
                <div
                    v-if="isOpen"
                    ref="dropdownContainer"
                    class="fixed z-[9999] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden"
                    :style="dropdownStyle"
                >
                <!-- Search box -->
                <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2 px-2 py-1.5 bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-400/20 transition-all">
                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 dark:text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        ref="searchInput"
                        v-model="search"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="w-full text-xs bg-transparent dark:text-slate-200 outline-none text-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500"
                        @keydown.escape="close"
                    />
                </div>
                </div>

                <!-- Options list -->
                <ul class="max-h-52 overflow-y-auto py-1">
                    <li
                        v-for="option in filteredOptions"
                        :key="option.value"
                        @click="select(option)"
                        class="flex items-center gap-2 px-3 py-2 text-sm cursor-pointer transition-colors duration-100"
                        :class="String(option.value) === String(modelValue)
                            ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold'
                            : 'text-slate-700 dark:text-slate-200 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                    >
                        <svg v-if="String(option.value) === String(modelValue)" class="w-3.5 h-3.5 text-brand-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span v-else class="w-3.5 flex-shrink-0"></span>
                        {{ option.label }}
                    </li>
                    <li v-if="filteredOptions.length === 0" class="px-3 py-4 text-center text-xs text-slate-400">
                        Tidak ada hasil untuk "{{ search }}"
                    </li>
                </ul>
            </div>
        </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Option {
    label: string;
    value: string | number;
}

interface Props {
    modelValue?: string | number;
    options: (Option | string)[];
    disabled?: boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const mappedOptions = computed(() => {
    return props.options.map((opt) => {
        if (typeof opt === 'string') {
            return { label: opt, value: opt };
        }
        return opt;
    });
});

const handleSelect = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    emit('update:modelValue', target.value);
};

const selectClasses = computed(() => {
    const base =
        'w-full px-4 h-10 bg-white border text-sm text-neutral-800 outline-none rounded-input appearance-none cursor-pointer transition-all duration-150 pr-10';

    if (props.disabled) {
        return `${base} border-neutral-200 bg-neutral-50 text-neutral-400 cursor-not-allowed`;
    }

    return `${base} border-neutral-200 focus:border-neutral-300 focus:ring-1 focus:ring-neutral-200`;
});
</script>

<template>
    <div class="relative w-full">
        <select :id="id" :value="modelValue" :disabled="disabled" :class="selectClasses" @change="handleSelect">
            <slot></slot>
            <option v-for="opt in mappedOptions" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>
        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-neutral-400 pointer-events-none">
            <iconify-icon icon="tabler:chevron-down" class="text-base"></iconify-icon>
        </span>
    </div>
</template>

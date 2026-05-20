<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue?: string | number;
    type?: string;
    placeholder?: string;
    disabled?: boolean;
    required?: boolean;
    error?: string | boolean;
    id?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    type: 'text',
    placeholder: '',
    disabled: false,
    required: false,
    error: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value);
};

const inputClasses = computed(() => {
    const base =
        'w-full px-4 h-10 bg-white border text-sm text-neutral-800 placeholder-neutral-400 outline-none rounded-input transition-all duration-150';

    if (props.error) {
        return `${base} border-red-300 focus:border-red-500 focus:ring-1 focus:ring-red-100`;
    }

    if (props.disabled) {
        return `${base} border-neutral-200 bg-neutral-50 text-neutral-400 cursor-not-allowed`;
    }

    return `${base} border-neutral-200 focus:border-neutral-300 focus:ring-1 focus:ring-neutral-200`;
});
</script>

<template>
    <div class="relative w-full">
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :required="required"
            :class="inputClasses"
            @input="handleInput"
        />
        <p v-if="typeof error === 'string' && error" class="mt-1 text-xs text-red-500 font-medium">
            {{ error }}
        </p>
    </div>
</template>

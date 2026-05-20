<script setup lang="ts">
interface Props {
    modelValue: string;
    type?: string;
    placeholder?: string;
    id?: string;
    required?: boolean;
}
defineProps<Props>();
defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
    <textarea
        v-if="type === 'textarea'"
        :id="id"
        :placeholder="placeholder"
        :required="required"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        rows="4"
        class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-slate-700 focus:ring-1 focus:ring-slate-700/50 resize-y"
    ></textarea>
    <input
        v-else
        :type="type || 'text'"
        :id="id"
        :placeholder="placeholder"
        :required="required"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        class="w-full bg-slate-950/80 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-slate-700 focus:ring-1 focus:ring-slate-700/50"
    />
</template>

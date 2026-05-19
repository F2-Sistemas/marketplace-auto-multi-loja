<script setup lang="ts">
interface Option {
  value: string | number;
  label: string;
}

interface Props {
  modelValue: string | number;
  label?: string;
  options: Option[];
  required?: boolean;
  disabled?: boolean;
}

defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: any): void;
}>();

const onChange = (event: Event) => {
  emit('update:modelValue', (event.target as HTMLSelectElement).value);
};
</script>

<template>
  <div class="space-y-1.5 w-full">
    <label
      v-if="label"
      class="block text-xs font-semibold text-slate-400 uppercase tracking-wide"
    >
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>
    
    <div class="relative">
      <select
        :value="modelValue"
        @change="onChange"
        :disabled="disabled"
        class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 rounded-xl py-3 px-4 text-sm text-slate-200 focus:outline-none transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed appearance-none cursor-pointer"
      >
        <option
          v-for="opt in options"
          :key="opt.value"
          :value="opt.value"
        >
          {{ opt.label }}
        </option>
      </select>
      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500">
        <iconify-icon icon="tabler:chevron-down" class="text-sm"></iconify-icon>
      </div>
    </div>
  </div>
</template>

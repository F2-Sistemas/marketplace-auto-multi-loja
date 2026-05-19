<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  modelValue: string | number;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  suffix?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  placeholder: '',
  required: false,
  disabled: false,
  error: '',
  suffix: '',
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: any): void;
}>();

const onInput = (event: Event) => {
  emit('update:modelValue', (event.target as HTMLInputElement).value);
};

const inputBorderClass = computed(() => {
  if (props.error) return 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/20';
  return 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20';
});
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
    
    <div class="flex relative rounded-xl shadow-inner">
      <input
        :type="type"
        :value="modelValue"
        @input="onInput"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[
          'flex-1 bg-slate-950 border py-3 px-4 text-sm text-slate-200 focus:outline-none focus:ring-2 transition-all duration-200 placeholder-slate-600 disabled:opacity-50 disabled:cursor-not-allowed',
          suffix ? 'rounded-l-xl' : 'rounded-xl',
          inputBorderClass
        ]"
      />
      <span
        v-if="suffix"
        class="bg-slate-800 border border-l-0 border-slate-850 px-4 flex items-center text-xs text-slate-400 rounded-r-xl"
      >
        {{ suffix }}
      </span>
    </div>
    
    <p v-if="error" class="text-xs text-rose-400 font-medium">
      {{ error }}
    </p>
  </div>
</template>

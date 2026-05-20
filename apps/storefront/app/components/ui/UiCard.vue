<script setup lang="ts">
interface Props {
    hover?: boolean;
}
withDefaults(defineProps<Props>(), {
    hover: false,
});
</script>

<template>
    <div
        :class="[
            'bg-slate-900/50 backdrop-blur-md border border-slate-800/80 rounded-xl overflow-hidden transition-all duration-300',
            hover ? 'hover:-translate-y-1 hover:border-slate-700/60 hover:shadow-lg hover:shadow-slate-950/50' : '',
        ]"
    >
        <slot></slot>
    </div>
</template>

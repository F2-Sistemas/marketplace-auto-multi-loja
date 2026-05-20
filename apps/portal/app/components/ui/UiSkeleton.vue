<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    type?: 'text' | 'rect' | 'circle';
    height?: string;
    width?: string;
    radius?: 'button' | 'input' | 'card' | 'badge' | 'full' | 'none';
}

const props = withDefaults(defineProps<Props>(), {
    type: 'rect',
    height: '1rem',
    width: '100%',
    radius: 'none',
});

const classes = computed(() => {
    const base = 'bg-neutral-150 animate-pulse';

    const shapes = {
        text: 'rounded-md h-3.5 my-1.5',
        rect: '',
        circle: 'rounded-full',
    };

    const radii = {
        button: 'rounded-button',
        input: 'rounded-input',
        card: 'rounded-card',
        badge: 'rounded-badge',
        full: 'rounded-full',
        none: 'rounded-none',
    };

    return [base, shapes[props.type], props.type !== 'text' && props.type !== 'circle' ? radii[props.radius] : ''].join(
        ' '
    );
});

const styles = computed(() => {
    if (props.type === 'text') return {};
    return {
        height: props.height,
        width: props.width,
    };
});
</script>

<template>
    <div :class="classes" :style="styles"></div>
</template>

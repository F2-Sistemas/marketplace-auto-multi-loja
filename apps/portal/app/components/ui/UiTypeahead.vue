<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue';

export type TypeaheadOption = {
    value: unknown;
    label: string;
};

type RefreshOptionsParams = {
    searchTerm: string;
};

type FilterOptionsParams = {
    searchTerm: string;
    options: TypeaheadOption[];
};

type SetOptionsCallback = (options: TypeaheadOption[]) => void;

interface Props {
    modelValue?: unknown;
    typeahead?: boolean;
    disabled?: boolean;
    initialOptionsMode?: 'on_mount' | 'on_click';
    initialOptions?: TypeaheadOption[] | (() => TypeaheadOption[] | Promise<TypeaheadOption[]>);
    refreshOptions?: ((params: RefreshOptionsParams) => TypeaheadOption[] | Promise<TypeaheadOption[]>) | null;
    filterOptionsHandler?:
        | ((params: FilterOptionsParams, setOptions: SetOptionsCallback) => TypeaheadOption[] | void)
        | null;
    initialValue?: unknown;
    searchValue?: string;
    placeholder?: string;
    emptyText?: string;
    loadingText?: string;
    wrapperClass?: string;
    selectClass?: string;
    optionClass?: string;
    dropdownClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: undefined,
    typeahead: true,
    disabled: false,
    initialOptionsMode: 'on_mount',
    initialOptions: undefined,
    refreshOptions: null,
    filterOptionsHandler: null,
    initialValue: undefined,
    searchValue: '',
    placeholder: 'Selecione uma opção',
    emptyText: 'Nenhuma opção encontrada',
    loadingText: 'Carregando...',
    wrapperClass: '',
    selectClass: '',
    optionClass: '',
    dropdownClass: '',
});

const emit = defineEmits<{
    'update:modelValue': [value: unknown];
    search: [payload: { searchTerm: string }];
    input: [value: unknown];
    before_change: [payload: { newValue: unknown; oldValue: unknown }];
    change: [payload: { value: unknown; option: TypeaheadOption | null }];
    open: [];
    close: [];
}>();

const DEBOUNCE_MS = 300;

const isOpen = ref(false);
const isLoading = ref(false);
const searchTerm = ref(props.searchValue || '');
const loadedOptions = ref<TypeaheadOption[]>([]);
const hasLoadedInitialOptions = ref(false);
const debounceTimer = ref<ReturnType<typeof setTimeout> | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);

const currentValue = computed(() => {
    if (props.modelValue !== undefined) {
        return props.modelValue;
    }

    return props.initialValue;
});

function isSameValue(a: any, b: any) {
    if (a === b) return true;
    const isAEmpty = a === null || a === undefined || a === '';
    const isBEmpty = b === null || b === undefined || b === '';
    if (isAEmpty && isBEmpty) return true;
    if (isAEmpty || isBEmpty) return false;
    return String(a) === String(b);
}

const selectedOption = computed(() => {
    const val = currentValue.value;

    if (val === undefined || val === null || val === '') {
        return null;
    }

    return loadedOptions.value.find((opt) => isSameValue(opt.value, val)) ?? null;
});

const displayOptions = computed(() => {
    if (!props.typeahead || !searchTerm.value) {
        return loadedOptions.value;
    }

    if (props.filterOptionsHandler) {
        let result: TypeaheadOption[] = [];

        const setOptions: SetOptionsCallback = (opts) => {
            result = opts;
        };

        const returned = props.filterOptionsHandler(
            { searchTerm: searchTerm.value, options: loadedOptions.value },
            setOptions
        );

        return Array.isArray(returned) ? returned : result;
    }

    if (!props.refreshOptions) {
        const term = searchTerm.value.toLowerCase();

        return loadedOptions.value.filter((opt) => {
            return opt.label.toLowerCase().includes(term);
        });
    }

    return loadedOptions.value;
});

async function resolveInitialOptions(): Promise<TypeaheadOption[]> {
    if (!props.initialOptions) {
        return [];
    }

    if (Array.isArray(props.initialOptions)) {
        return props.initialOptions;
    }

    const result = await props.initialOptions();

    return Array.isArray(result) ? result : [];
}

async function loadInitialOptions() {
    if (hasLoadedInitialOptions.value) {
        return;
    }

    isLoading.value = true;

    try {
        loadedOptions.value = await resolveInitialOptions();
        hasLoadedInitialOptions.value = true;
    } finally {
        isLoading.value = false;
    }
}

async function handleOpen() {
    isOpen.value = true;

    emit('open');

    if (props.initialOptionsMode === 'on_click') {
        await loadInitialOptions();
    }

    if (props.typeahead) {
        await nextTick();
        searchInputRef.value?.focus();
    }
}

function handleClose() {
    isOpen.value = false;
    searchTerm.value = '';

    emit('close');
}

function handleTriggerClick() {
    if (props.disabled) {
        return;
    }

    if (isOpen.value) {
        handleClose();
        return;
    }

    handleOpen();
}

// When initialOptions is a reactive array (e.g. a computed), sync loadedOptions automatically.
watch(
    () => props.initialOptions,
    (newOptions) => {
        if (Array.isArray(newOptions)) {
            loadedOptions.value = newOptions;
            hasLoadedInitialOptions.value = true;
        }
    },
    { deep: false }
);

function handleSelect(option: TypeaheadOption) {
    const oldValue = currentValue.value;

    emit('before_change', { newValue: option.value, oldValue });
    emit('update:modelValue', option.value);
    emit('input', option.value);
    emit('change', { value: option.value, option });

    handleClose();
}

watch(searchTerm, (newTerm) => {
    emit('search', { searchTerm: newTerm });

    if (!props.typeahead || !props.refreshOptions) {
        return;
    }

    if (debounceTimer.value) {
        clearTimeout(debounceTimer.value);
    }

    isLoading.value = true;

    debounceTimer.value = setTimeout(async () => {
        try {
            const results = await props.refreshOptions!({ searchTerm: newTerm });

            loadedOptions.value = Array.isArray(results) ? results : [];
        } catch {
            loadedOptions.value = [];
        } finally {
            isLoading.value = false;
        }
    }, DEBOUNCE_MS);
});

onMounted(async () => {
    if (props.initialOptionsMode === 'on_mount') {
        await loadInitialOptions();
    }
});
</script>

<template>
    <div class="relative" :class="wrapperClass">
        <!-- Trigger -->
        <button
            type="button"
            :disabled="disabled"
            :class="[
                'w-full flex items-center justify-between gap-2 h-10',
                'border border-neutral-200 bg-white px-4 text-sm text-neutral-800 outline-none rounded-input transition-all duration-150',
                'hover:border-neutral-300 focus:border-brand-500 focus:ring-1 focus:ring-brand-500',
                {
                    'border-brand-500 ring-1 ring-brand-200': isOpen,
                    'bg-neutral-50 text-neutral-400 cursor-not-allowed opacity-60 hover:border-neutral-200': disabled,
                },
                selectClass,
            ]"
            @click="handleTriggerClick"
        >
            <span :class="{ 'text-neutral-400': !selectedOption }">
                {{ selectedOption ? selectedOption.label : placeholder }}
            </span>

            <iconify-icon
                icon="tabler:chevron-down"
                class="h-4 w-4 shrink-0 text-neutral-400 transition-transform duration-200 block"
                :class="{ 'rotate-180': isOpen }"
            />
        </button>

        <!-- Backdrop -->
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-30" @click="handleClose" />
        </Transition>

        <!-- Dropdown -->
        <Transition name="scale-fade">
            <div
                v-if="isOpen"
                :class="[
                    'absolute top-full left-0 right-0 z-40 mt-1',
                    'rounded-lg border border-neutral-200 bg-white shadow-lg overflow-hidden',
                    dropdownClass,
                ]"
            >
                <!-- Search input (typeahead mode only) -->
                <div v-if="typeahead" class="border-b border-neutral-100 p-2">
                    <div class="relative">
                        <iconify-icon
                            icon="tabler:search"
                            class="absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-neutral-400 block"
                        />
                        <input
                            ref="searchInputRef"
                            v-model="searchTerm"
                            type="text"
                            class="w-full rounded-md border border-neutral-200 py-1.5 pl-8 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 placeholder:text-neutral-400"
                            placeholder="Buscar..."
                        />
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="isLoading" class="p-3 text-center text-sm text-neutral-500">
                    {{ loadingText }}
                </div>

                <!-- Empty -->
                <div
                    v-else-if="displayOptions.length === 0"
                    class="p-3 text-center text-sm text-neutral-500 bg-neutral-50"
                >
                    {{ emptyText }}
                </div>

                <!-- Options list -->
                <ul v-else class="max-h-60 overflow-y-auto divide-y divide-neutral-50">
                    <li
                        v-for="option in displayOptions"
                        :key="String(option.value)"
                        :class="[
                            'cursor-pointer transition-colors',
                            { 'bg-brand-50': isSameValue(option.value, currentValue) },
                            optionClass,
                        ]"
                    >
                        <button
                            type="button"
                            class="w-full px-3 py-2.5 text-left text-sm"
                            :class="{
                                'font-semibold text-brand-600': isSameValue(option.value, currentValue),
                                'text-neutral-700 hover:bg-neutral-50': !isSameValue(option.value, currentValue),
                            }"
                            @click="handleSelect(option)"
                        >
                            {{ option.label }}
                        </button>
                    </li>
                </ul>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 150ms ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.scale-fade-enter-active,
.scale-fade-leave-active {
    transition:
        opacity 150ms ease,
        transform 150ms ease;
}

.scale-fade-enter-from,
.scale-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px) scale(0.97);
}
</style>

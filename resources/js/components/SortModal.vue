<script setup lang="ts">
import { X } from 'lucide-vue-next';
import { computed, ref, toRefs, watch } from 'vue';

export interface SortField {
    key: string;
    label: string;
}

interface SortItem {
    field: string;
    order: 'asc' | 'desc';
}

const props = defineProps<{
    isOpen: boolean;
    fields: SortField[];
    // optional initial sort object: { name: 'ASC', created_at: 'DESC' }
    initialSort?: Record<string, string>;
}>();

const { isOpen, fields, initialSort } = toRefs(props);

const emit = defineEmits<{
    close: [];
    apply: [sorts: SortItem[]];
}>();

const sorts = ref<SortItem[]>([]);

// populate initial sorts if provided
if (initialSort?.value) {
    sorts.value = Object.entries(initialSort.value).map(([field, order]) => ({
        field,
        order: (order || 'ASC').toLowerCase() === 'desc' ? 'desc' : 'asc',
    }));
}

// watch for prop changes
watch(initialSort, (next) => {
    if (next) {
        sorts.value = Object.entries(next).map(([field, order]) => ({
            field,
            order: (order || 'ASC').toLowerCase() === 'desc' ? 'desc' : 'asc',
        }));
    } else {
        sorts.value = [];
    }
});

const availableFields = computed(() => {
    const selectedKeys = sorts.value.map(s => s.field);
    return (fields.value ?? []).filter(f => !selectedKeys.includes(f.key));
});

const addSort = () => {
    if (availableFields.value.length > 0) {
        sorts.value.push({
            field: availableFields.value[0].key,
            order: 'asc',
        });
    }
};

const removeSort = (index: number) => {
    sorts.value.splice(index, 1);
};

const toggleOrder = (index: number) => {
    sorts.value[index].order = sorts.value[index].order === 'asc' ? 'desc' : 'asc';
};

const applySort = () => {
    // debug: emitted sorts
    // eslint-disable-next-line no-console
    console.log('SortModal: apply sorts', JSON.parse(JSON.stringify(sorts.value)));
    emit('apply', sorts.value);
    emit('close');
};

const resetSort = () => {
    sorts.value = [];
    emit('apply', []);
    emit('close');
};

</script>

<template>
    <!-- Backdrop -->
    <div v-if="isOpen" class="fixed inset-0 bg-black/50 z-40" @click="$emit('close')"></div>

    <!-- Modal -->
    <div v-if="isOpen" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg w-full max-w-md z-50">
        <div class="p-6">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Sort By</h2>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <!-- Sort Items -->
            <div class="space-y-3 mb-6">
                <div v-if="sorts.length === 0" class="text-sm text-gray-500 dark:text-gray-400 py-2">
                    No sorts added. Click "Add Sort" to get started.
                </div>

                <div v-for="(sort, index) in sorts" :key="index" class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <!-- Field Select -->
                    <select
                        v-model="sort.field"
                        class="flex-1 px-3 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option v-for="field in fields" :key="field.key" :value="field.key">
                            {{ field.label }}
                        </option>
                    </select>

                    <!-- Order Toggle -->
                    <button
                        @click="toggleOrder(index)"
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition whitespace-nowrap"
                    >
                        {{ sort.order.toUpperCase() }}
                    </button>

                    <!-- Remove Button -->
                    <button
                        @click="removeSort(index)"
                        class="px-2 py-1 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium text-sm"
                    >
                        Remove
                    </button>
                </div>
            </div>

            <!-- Add Sort Button -->
            <button
                v-if="availableFields.length > 0"
                @click="addSort"
                class="w-full mb-4 px-4 py-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500 font-medium text-sm transition"
            >
                + Add Sort
            </button>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button
                    @click="resetSort"
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-medium transition"
                >
                    Reset
                </button>
                <button
                    @click="applySort"
                    class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition"
                >
                    Apply
                </button>
            </div>
        </div>
    </div>
</template>

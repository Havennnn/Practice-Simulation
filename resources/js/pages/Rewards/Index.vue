<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rewards',
        href: '#',
    },
];

const props = defineProps<{
    rewards: any;
}>();

const getLogoUrl = (logoFile: any): string => {
    if (logoFile?.file_path) {
        return `/storage/${logoFile.file_path}`;
    }
    return 'https://placeholdr.dev/320x320/cat?style=anime';
};

const meta = computed(() => props.rewards?.meta ?? {});
const from = computed(() => meta.value.from ?? 0);
const to = computed(() => meta.value.to ?? 0);
const total = computed(() => meta.value.total ?? (props.rewards?.data?.length ?? 0));
const currentPage = computed(() => meta.value.current_page ?? meta.value.currentPage ?? 1);
const lastPage = computed(() => meta.value.last_page ?? meta.value.lastPage ?? 1);

const pages = computed(() => {
    const cp = Number(currentPage.value || 1);
    const lp = Number(lastPage.value || 1);
    const start = Math.max(1, cp - 2);
    const end = Math.min(lp, cp + 2);
    const out: number[] = [];
    for (let i = start; i <= end; i++) out.push(i);
    return out;
});

function goToPage(page: number) {
    if (!page || page < 1 || page === currentPage.value || page > lastPage.value) return;
    router.get(window.location.pathname, { page }, { preserveState: true });
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

</script>

<template>
    <Head title="Rewards" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-between my-2">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rewards</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your rewards</p>
                </div>
                <a href="/rewards/create" class="px-6 py-2 border border-sidebar-border/70 hover:bg-gray-50/10 cursor-pointer text-white font-medium rounded-lg transition">
                    + Create Reward
                </a>
            </div>
            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border overflow-hidden"
            >
                <table class="w-full text-sm">
                    <thead class="border border-b">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white w-2/5">Name</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white w-2/5">Description</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white w-2/5">Points</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white w-1/5">Date Created</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-900 dark:text-white w-1/5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="reward in props.rewards.data" :key="reward.id" class="hover:bg-gray-50/10 dark:hover:bg-gray-800/10 transition">
                            <!-- Logo + Name + Code + Points -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <img :src="getLogoUrl(reward.logoFile)" :alt="reward.name" class="w-14 h-14 rounded object-cover flex-shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-4">
                                            <span class="font-semibold text-gray-900 dark:text-white uppercase truncate">{{ reward.name }}</span>
                                        </div>
                                        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400 truncate">{{ reward.code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-gray-600 dark:text-gray-400 max-w-prose line-clamp-2">{{ reward.description }}</td>
                            <td class="px-6 py-5 text-gray-600 dark:text-gray-400">{{ reward.points }} pts</td>
                            <td class="px-6 py-5 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ reward.createdAt }}</td>
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center gap-3">
                                    <button class="cursor-pointer text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">Edit</button>
                                    <button class="cursor-pointer text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium">Delete</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Pagination -->
                <div class="px-6 py-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-800">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Showing <span class="font-medium">{{ from }}</span> to <span class="font-medium">{{ to }}</span> of <span class="font-medium">{{ total }}</span>
                    </div>

                    <div class="inline-flex items-center space-x-2">
                        <button
                            :disabled="currentPage <= 1"
                            @click="goToPage(currentPage - 1)"
                            class="px-3 py-1 rounded-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 disabled:opacity-50"
                        >
                            Previous
                        </button>

                        <template v-for="p in pages" :key="p">
                            <button
                                @click="goToPage(p)"
                                :class="[
                                    'px-3 py-1 rounded-md text-sm',
                                    p === currentPage ? 'bg-gray-200 dark:bg-gray-700 font-semibold' : 'bg-white dark:bg-gray-900'
                                ]"
                            >
                                {{ p }}
                            </button>
                        </template>

                        <button
                            :disabled="currentPage >= lastPage"
                            @click="goToPage(currentPage + 1)"
                            class="px-3 py-1 rounded-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

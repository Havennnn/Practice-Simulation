<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Download, ImagePlus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rewards',
        href: '/rewards',
    },
    {
        title: 'Create',
        href: '#',
    },
];

const props = defineProps<{
    code?: string;
}>();

const form = useForm({
  code: props.code ?? '',
  name: '',
  points: 0,
  description: '',
  logo: null as File | null,
});

const selectedFile = ref<File | null>(null);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files?.[0]) {
        selectedFile.value = target.files[0];
        form.logo = target.files[0];
    }
};

const removeFile = () => {
    selectedFile.value = null;
    form.logo = null;
};

const handleGenerateCode = async () => {
    try {
        const response = await fetch('/rewards/generate-code', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.code) {
            form.code = data.code;
        }
    } catch (error) {
        console.error('Failed to generate code:', error);
    }
};

const handleSubmit = () => {
  form.post('/rewards');
};

</script>

<template>
    <Head title="Create Reward" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-center min-h-[calc(100vh-4rem)] p-6">
            <div class="w-full max-w-md border border-sidebar-border/70 dark:border-sidebar-border rounded-lg overflow-hidden p-6">
                <form @submit.prevent="handleSubmit" class="w-full">
                    <div class="flex flex-col gap-6">
                        <!-- Header -->
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Create Reward</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Add a new reward to your catalog</p>
                        </div>

                        <!-- Code + Generate (inline) -->
                        <div class="grid gap-2">
                            <Label for="code">Code</Label>
                            <div class="flex">
                                <Input
                                    id="code"
                                    type="text"
                                    name="code"
                                    required
                                    placeholder="e.g. REW-12345"
                                    class="flex-1"
                                    v-model="form.code"
                                    readonly
                                />
                                <button
                                    type="button"
                                    @click="handleGenerateCode"
                                    class="ml-2 whitespace-nowrap cursor-pointer inline-flex items-center justify-center px-3 py-2 rounded-md bg-white/5 hover:bg-white/10 text-sm font-medium border border-gray-300 dark:border-gray-700"
                                >
                                    Generate
                                </button>
                            </div>
                            <InputError :message="form.errors.code" />
                        </div>

                        <!-- Title -->
                        <div class="grid gap-2">
                            <Label for="name">Title</Label>
                            <Input id="name" name="name" type="text" placeholder="Reward title" required v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Points with unit -->
                        <div class="grid gap-2">
                            <Label for="points">Points</Label>
                            <div class="flex">
                                <Input id="points" name="points" type="number" min="0" placeholder="0" v-model="form.points" />
                                <div class="ml-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 flex items-center">Pts.</div>
                            </div>
                            <InputError :message="form.errors.points" />
                        </div>

                        <!-- Description -->
                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <textarea id="description" name="description" rows="4" placeholder="Enter description" required v-model="form.description" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-transparent"></textarea>
                            <InputError :message="form.errors.description" />
                        </div>

                        <!-- Image upload (compact) -->
                        <div class="grid gap-2">
                            <Label>Image</Label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition">
                                <input type="file" name="logo" accept="image/*" @change="handleFileSelect" class="hidden" id="file-input" />
                                <label for="file-input" class="flex flex-col items-center justify-center cursor-pointer">
                                    <ImagePlus class="w-10 h-10 text-orange-500 mb-2" />
                                    <p class="text-orange-500 font-medium">Select a file or drag and drop</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">JPG, PNG upto 10MB</p>
                                </label>
                            </div>
                            <InputError :message="form.errors.logo" />

                            <div v-if="selectedFile" class="mt-3 p-3 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <ImagePlus class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ selectedFile.name }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ Math.round(selectedFile.size / 1024) }}KB</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" class="p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition"><Download class="w-5 h-5" /></button>
                                    <button type="button" @click="removeFile" class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"><Trash2 class="w-5 h-5" /></button>
                                </div>
                            </div>
                        </div>

                        <!-- Submit (login/register style: full width) -->
                        <div>
                            <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                                <Spinner v-if="form.processing" />
                                Create Reward
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

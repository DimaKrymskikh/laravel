<script setup>
import { inject } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';

defineProps({
    text: String
});

const app = inject('app');
</script>

<template>
    <button class="px-2 py-1 rounded-lg bg-orange-300 text-orange-700 cursor-not-allowed" disabled v-if="app.isRequest">
        <Spinner styleSpinner="h-6 fill-gray-700 text-gray-200" class="flex justify-center" />
    </button>
    <button class="px-2 py-1 rounded-lg bg-orange-300 text-orange-600 hover:bg-orange-400 hover:text-orange-800" v-else>
        {{ text }}
    </button>
</template>

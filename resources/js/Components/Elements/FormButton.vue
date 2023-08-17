<script setup>
import Spinner from '@/components/Svg/Spinner.vue';

defineProps({
    processing: Boolean,
    isRequest: Boolean,
    text: String
});
</script>

<template>
    <button class="px-2 py-1 rounded-lg bg-orange-300 text-orange-700 hover:bg-orange-200 text-orange-600" :disabled="processing">
        <Spinner hSpinner="h-6" class="flex justify-center" v-if="isRequest" />
        <span v-else>{{ text }}</span>
    </button>
</template>

<script setup>
defineProps({
    buttonText: String,
    handler: Function
});
</script>

<template>
    <button
        class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded-lg"
        @click="handler"
    >
        {{ buttonText }}
    </button>
</template>


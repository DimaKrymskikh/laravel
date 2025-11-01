<script setup>
defineProps({
    buttonText: String
});
</script>

<template>
    <button
        class="px-4 py-2 bg-gray-400 text-gray-950 rounded-lg"
    >
        {{ buttonText }}
    </button>
</template>

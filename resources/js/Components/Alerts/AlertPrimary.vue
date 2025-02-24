<script setup>
    
const props = defineProps({
    text: String
});

</script>

<template>
    <div class="text-orange-600 bg-orange-100 my-2 p-4">
        {{ text }}
    </div>
</template>

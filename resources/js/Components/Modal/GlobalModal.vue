<script setup>
import Spinner from '@/components/Svg/Spinner.vue';
</script>

<template>
    <div
        class="fixed top-0 left-0 right-0 w-full overflow-x-hidden overflow-y-auto h-screen"
        tabindex="-1"
    >
        <div id="modal-background" class="fixed opacity-25 bg-gray-500 w-full h-screen z-5"></div>
        <Spinner class="absolute top-1/3 left-1/2 z-10" styleSpinner="h-48 fill-orange-700 text-orange-200" />
    </div>
</template>

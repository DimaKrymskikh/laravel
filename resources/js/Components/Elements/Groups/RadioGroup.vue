<script setup>
import { app } from '@/Services/app';
import Spinner from '@/components/Svg/Spinner.vue';
</script>

<template>
    <div class="relative" v-if="app.isRequest">
        <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
    </div>
    <slot></slot>
</template>

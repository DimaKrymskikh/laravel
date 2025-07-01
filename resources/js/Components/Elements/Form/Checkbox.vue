<script setup>
import { app } from '@/Services/app';
    
const props = defineProps({
    titleText: String,
    modelValue: Boolean
});

defineEmits(['update:modelValue']);
</script>

<template>
    <label>
        <span class="text-orange-700">{{ titleText }}</span>
        <input v-if="app.isRequest"
            type="checkbox"
            :checked="modelValue"
            disabled
        />
        <input v-else
            type="checkbox"
            :checked="modelValue"
            @input="$emit('update:modelValue', $event.target.checked)"
        />
    </label>
</template>

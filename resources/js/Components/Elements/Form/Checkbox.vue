<script setup>
import { inject } from 'vue';
    
const props = defineProps({
    titleText: String,
    modelValue: Boolean
});

defineEmits(['update:modelValue']);

const app = inject('app');

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

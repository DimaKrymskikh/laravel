<script setup>
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';
import { useAutofocus } from '@/Services/form';

const props = defineProps({
    errorsMessage: String | undefined,
    modelValue: String | Number,
    handler: Function
});

defineEmits(['update:modelValue']);

// SimpleInput всегда получает фокус
const input = useAutofocus(true);
</script>

<template>
    <div class="relative" v-if="app.isRequest">
        <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
        <input
            class="disabled font-sans"
            type="text"
            :value="modelValue"
            disabled
        />
    </div>
    <div v-else>
        <input
            :class="$attrs.class"
            class="font-sans"
            type="text"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @change="handler"
            ref="input"
        />
    </div>
    <div v-if="errorsMessage" class="error bottom-10 text-sm font-medium">{{ errorsMessage }}</div>
</template>

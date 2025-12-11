<script setup>
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';
import { useAutofocus } from '@/Services/form';
    
const props = defineProps({
    titleText: String,
    modelValue: Boolean,
    hide: Function | undefined,
    handler: Function
});

defineEmits(['update:modelValue']);

const checkbox = useAutofocus(true);
</script>

<template>
    <label>
        <span class="text-orange-700">{{ titleText }}</span>
        <div class="relative" v-if="app.isRequest">
            <Spinner class="spinner" styleSpinner="h-4 fill-gray-700 text-gray-200" />
        </div>
        <input v-if="app.isRequest"
            type="checkbox"
            :checked="modelValue"
            disabled
        />
        <input v-else
            type="checkbox"
            :checked="modelValue"
            @input="$emit('update:modelValue', $event.target.checked)"
            @change="handler"
            @blur="hide"
            ref="checkbox"
        />
    </label>
</template>

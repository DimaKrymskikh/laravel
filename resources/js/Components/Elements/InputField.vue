<script setup>
import Spinner from '@/components/Svg/Spinner.vue';

defineProps({
    titleText: String,
    type: String,
    errorsMessage: String,
    modelValue: String,
    isRequest: Boolean
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="mb-2">
        <label>
            <span class="text-orange-700">{{ titleText }}</span>
            <Spinner hSpinner="h-8" class="flex justify-center" v-if="isRequest"/>
            <input v-else
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
            />
        </label>
        <div v-if="errorsMessage" class="error">{{ errorsMessage }}</div>
    </div>
</template>

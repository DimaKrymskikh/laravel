<script setup>
import { ref, onMounted, onUpdated } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';
import { useAutofocus } from '@/Services/form';

const props = defineProps({
    titleText: String,
    type: String,
    isInputDisabled: Boolean | undefined,
    isInputAutofocus: Boolean | undefined,
    errorsMessage: String | undefined,
    modelValue: String | Number
});

defineEmits(['update:modelValue']);

const input = useAutofocus(props.isInputAutofocus);
</script>

<template>
    <div class="relative h-28">
        <label>
            <span class="text-orange-700">{{ titleText }}</span>
            <div class="relative" v-if="app.isRequest">
                <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
                <input
                    class="disabled font-sans"
                    :type="type"
                    :value="modelValue"
                    disabled
                />
            </div>
            <div class="relative" v-else>
                <input
                    class="font-sans"
                    :class="isInputDisabled ? 'cursor-not-allowed' : ''"
                    :type="type"
                    :disabled="isInputDisabled"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    ref="input"
                />
            </div>
            <div v-if="errorsMessage" class="error bottom-10 text-sm font-medium">{{ errorsMessage }}</div>
        </label>
    </div>
</template>

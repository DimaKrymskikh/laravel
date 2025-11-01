<script setup>
import { ref, onMounted, onUpdated } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';
import { useAutofocus } from '@/Services/form';

const props = defineProps({
    titleText: String,
    isTextareaDisabled: Boolean | undefined,
    isTextareaAutofocus: Boolean | undefined,
    errorsMessage: String | undefined,
    modelValue: String | Number
});

defineEmits(['update:modelValue']);

const textarea = useAutofocus(props.isTextareaAutofocus);
</script>

<template>
    <div class="relative h-48">
        <label>
            <span class="text-orange-700">{{ titleText }}</span>
            <div class="relative" v-if="app.isRequest">
                <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
                <textarea
                    class="disabled font-sans"
                    :rows="props.rows"
                    disabled
                ></textarea>
            </div>
            <div class="relative" v-else>
                <textarea
                    class="font-sans"
                    rows="5"
                    :class="isTextareaDisabled ? 'cursor-not-allowed' : ''"
                    :disabled="isTextareaDisabled"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    ref="textarea"
                ></textarea>
            </div>
            <div v-if="errorsMessage" class="error bottom-10 text-sm font-medium">{{ errorsMessage }}</div>
        </label>
    </div>
</template>

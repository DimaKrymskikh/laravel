<script setup>
import { inject, ref, onMounted, onUpdated } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';

const props = defineProps({
    titleText: String,
    type: String,
    isInputDisabled: Boolean | undefined,
    isInputAutofocus: Boolean | undefined,
    errorsMessage: String | undefined,
    modelValue: String
});

const app = inject('app');

defineEmits(['update:modelValue']);

// Если в форме несколько полей InputField, то
// autofocus может быть только у одного
const input = props.isInputAutofocus ? ref(null) : null;

onMounted(() => {
    if(props.isInputAutofocus && input.value) {
        input.value.focus();
    }
});

onUpdated(() => {
    if(props.isInputAutofocus && input.value) {
        input.value.focus();
    }
});
</script>

<template>
    <div>
        <label>
            <span class="text-orange-700">{{ titleText }}</span>
            <div class="relative h-20" v-if="app.isRequest">
                <Spinner class="absolute top-2 left-1/2 z-10" styleSpinner="h-6 fill-gray-700 text-gray-200" />
                <input
                    class="absolute disabled font-sans"
                    :type="type"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    disabled
                />
            </div>
            <div class="relative h-20" v-else>
                <input
                    class="absolute font-sans"
                    :class="isInputDisabled ? 'cursor-not-allowed' : ''"
                    :type="type"
                    :disabled="isInputDisabled"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    ref="input"
                />
                <div v-if="errorsMessage" class="error absolute top-10 text-sm font-medium">{{ errorsMessage }}</div>
            </div>
        </label>
    </div>
</template>

<script setup>
import { ref, onMounted, onUpdated } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';
import { useAutofocus } from '@/Services/form';

const props = defineProps({
    rows: Number | undefined,
    errorsMessage: String | undefined,
    modelValue: String | Number,
    hide: Function | undefined,
    handler: Function
});

const rows = props.rows ? props.rows : '3';

defineEmits(['update:modelValue']);

// SimpleTextarea всегда получает фокус
const textarea = useAutofocus(true);
</script>

<template>
    <div class="relative" v-if="app.isRequest">
        <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
        <textarea
            class="disabled font-sans"
            :rows="rows"
            :value="modelValue"
            disabled
        ></textarea>
    </div>
    <div v-else>
        <textarea
            class="font-sans"
            :rows="rows"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @change="handler"
            @blur="hide"
            ref="textarea"
        ></textarea>
    </div>
    <div v-if="errorsMessage" class="error bottom-10 text-sm font-medium">{{ errorsMessage }}</div>
</template>

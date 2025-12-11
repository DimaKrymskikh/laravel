<script setup>
import { app } from '@/Services/app';

const props = defineProps({
    titleText: String,
    name: String,
    isChecked: Boolean,
    radioValue: String | Number,
    modelValue: String | Number
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="my-2">
        <label class="text-right">
            <span>
                <input
                    class="inline font-sans w-8"
                    type="radio"
                    :name="name"
                    :value="radioValue"
                    @click="$emit('update:modelValue', $event.target.value)"
                    :checked="isChecked"
                    :disabled ="app.isRequest"
                />
            </span>
            <span class="text-orange-700 items-center">{{ titleText }}</span>
        </label>
    </div>
</template>

<script setup>
defineProps({
    titleText: String,
    type: String,
    errorsMessage: String,
    modelValue: String
});
defineEmits(['update:modelValue']);
</script>

<template>
    <label>{{ titleText }}
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </label>
    <div v-if="errorsMessage" class="error">{{ errorsMessage }}</div>
</template>

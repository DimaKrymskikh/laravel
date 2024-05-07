<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    datepicker: String,
    modelValue: String
});

onMounted(() => {
    pikaday(props.datepicker);
});

defineEmits(['update:modelValue']);
    
</script>

<template>
    <input 
        :id="datepicker"
        class="w-32 font-sans"
        type="text"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    />
</template>


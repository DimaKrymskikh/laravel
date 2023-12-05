<script setup>
import { ref } from 'vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';

const isShowAddLanguageModal = ref(false);

const showAddLanguageModal = function() {
    isShowAddLanguageModal.value = true;
};
const hideAddLanguageModal = function() {
    isShowAddLanguageModal.value = false;
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить язык"
        :handler="showAddLanguageModal"
    />
    <AddLanguageModal
        :hideAddLanguageModal="hideAddLanguageModal"
        v-if="isShowAddLanguageModal"
    />
</template>

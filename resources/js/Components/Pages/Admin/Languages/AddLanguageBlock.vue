<script setup>
import { ref } from 'vue';
import { language } from '@/Services/Content/languages';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';

const showModal = function() {
    language.showAddLanguageModal();
};

</script>

<template>
    <PrimaryButton
        buttonText="Добавить язык"
        :handler="showModal"
    />
    <AddLanguageModal
        v-if="language.isShowAddLanguageModal"
    />
</template>

<script setup>
import { ref } from 'vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';

const isShowAddCityModal = ref(false);

const showAddCityModal = function() {
    isShowAddCityModal.value = true;
};
const hideAddCityModal = function() {
    isShowAddCityModal.value = false;
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить город"
        :handler="showAddCityModal"
    />
    <AddCityModal
        :hideAddCityModal="hideAddCityModal"
        v-if="isShowAddCityModal"
    />
</template>

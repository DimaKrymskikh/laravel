<script setup>
import { ref } from 'vue';
import { city } from '@/Services/Content/cities';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';

const showModal = function() {
    city.showAddCityModal();
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить город"
        :handler="showModal"
    />
    <AddCityModal
        v-if="city.isShowAddCityModal"
    />
</template>

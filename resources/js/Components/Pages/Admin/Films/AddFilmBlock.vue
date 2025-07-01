<script setup>
import { ref } from 'vue';
import { film } from '@/Services/Content/films';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';

const showModal = function() {
    film.showAddFilmModal();
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить фильм"
        :handler="showModal"
    />
    <AddFilmModal
        v-if="film.isShowAddFilmModal"
    />
</template>

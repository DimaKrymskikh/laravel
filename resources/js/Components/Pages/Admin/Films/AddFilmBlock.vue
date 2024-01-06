<script setup>
import { ref } from 'vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';

const isShowAddFilmModal = ref(false);

const showAddFilmModal = function() {
    isShowAddFilmModal.value = true;
};
const hideAddFilmModal = function() {
    isShowAddFilmModal.value = false;
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить фильм"
        :handler="showAddFilmModal"
    />
    <AddFilmModal
        :hideAddFilmModal="hideAddFilmModal"
        v-if="isShowAddFilmModal"
    />
</template>

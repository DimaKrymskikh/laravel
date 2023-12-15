<script setup>
import { ref } from 'vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddActorModal from '@/Components/Modal/Request/Actors/AddActorModal.vue';

const isShowAddActorModal = ref(false);

const showAddActorModal = function() {
    isShowAddActorModal.value = true;
};
const hideAddActorModal = function() {
    isShowAddActorModal.value = false;
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить актёра"
        :handler="showAddActorModal"
    />
    <AddActorModal
        :hideAddActorModal="hideAddActorModal"
        v-if="isShowAddActorModal"
    />
</template>

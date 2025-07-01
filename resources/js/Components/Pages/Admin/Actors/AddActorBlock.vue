<script setup>
import { ref } from 'vue';
import { actor } from '@/Services/Content/actors';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddActorModal from '@/Components/Modal/Request/Actors/AddActorModal.vue';

const showModal = function() {
    actor.showAddActorModal();
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить актёра"
        :handler="showModal"
    />
    <AddActorModal
        v-if="actor.isShowAddActorModal"
    />
</template>

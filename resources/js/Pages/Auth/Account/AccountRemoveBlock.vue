<script setup>
import { ref } from 'vue';
import DangerButton from '@/Components/Buttons/Variants/DangerButton.vue';
import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';

const isShowAccountRemoveModal = ref(false);

const showAccountRemoveModal = function() {
    isShowAccountRemoveModal.value = true;
};

const hideAccountRemoveModal = function() {
    isShowAccountRemoveModal.value = false;
};
</script>

<template>
    <DangerButton
        buttonText="Удалить аккаунт"
        :handler="showAccountRemoveModal"
    />

    <AccountRemoveModal
        :hideAccountRemoveModal="hideAccountRemoveModal"
        v-if="isShowAccountRemoveModal"
    />
</template>

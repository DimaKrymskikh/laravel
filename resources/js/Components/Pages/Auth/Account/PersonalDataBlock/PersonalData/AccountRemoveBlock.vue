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
    <div class="text-orange-900">
        Удаление аккаунта:
    </div>
    <div class="text-sm text-justify mb-2">
        Нажмите на кнопку "Удалить аккаунт", если хотите отказатся от аккаунта.
    </div>
    <div class="text-center mb-4">
        <DangerButton
            buttonText="Удалить аккаунт"
            :handler="showAccountRemoveModal"
        />
    </div>

    <AccountRemoveModal
        :hideAccountRemoveModal="hideAccountRemoveModal"
        v-if="isShowAccountRemoveModal"
    />
</template>

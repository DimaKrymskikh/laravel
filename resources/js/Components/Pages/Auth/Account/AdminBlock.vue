<script setup>
import { ref } from 'vue';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';

defineProps({
    user: Object | null
});

const isShowAdminModal = ref(false);

const showAdminModal = function() {
    isShowAdminModal.value = true;
};
const hideAdminModal = function() {
    isShowAdminModal.value = false;
};
</script>

<template>
    <div class="ml-4">
        <PrimaryButton
            buttonText="Отказаться от администрирования"
            :handler="showAdminModal"
            v-if="user.is_admin"
        />
        <PrimaryButton
            buttonText="Сделать себя админом"
            :handler="showAdminModal"
            v-else
        />
    </div>
        
    <AdminModal
        :hideAdminModal="hideAdminModal"
        :admin="user.is_admin"
        v-if="isShowAdminModal"
    />
</template>


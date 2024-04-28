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
    <div class="text-orange-900">
        Управление правами:
    </div>
    <div class="text-sm text-justify mb-2">
        <span v-if="user.is_admin">Нажмите кнопку "Отказаться от администрирования", чтобы не быть админом.</span>
        <span v-else>Нажмите кнопку "Сделать себя админом", чтобы получить права админа.</span>
    </div>
    <div class="text-center mb-4">
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


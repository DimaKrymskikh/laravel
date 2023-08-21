<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAdminModal, admin } = defineProps({
    hideAdminModal: Function,
    admin: Boolean
});

const filmsAccount = inject('filmsAccount');

const inputPassword = ref('');
const errorsPassword = ref('');
const isRequest = ref(false);

const handlerSubmit = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    let url = admin ? 'admin/destroy' : 'admin/create';
    
    router.post(url, {
            password: inputPassword.value,
            page: filmsAccount.page,
            number: filmsAccount.perPage,
            title: filmsAccount.title,
            description: filmsAccount.description
        }, {
        onBefore: () => {
            isRequest.value = true;
            errorsPassword.value = '';
        },
        onSuccess: () => {
            hideAdminModal();
        },
        onError: errors => {
            errorsPassword.value = errors.password;
        },
        onFinish: () => {
            isRequest.value = false;
        }
    });
};

</script>

<template>
    <BaseModal
        modalId="admin-modal"
        :headerTitle="admin ? 'Отказ от статуса админа' : 'Подтверждение статуса админа'"
        :hideModal="hideAdminModal"
        :handlerSubmit="handlerSubmit"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            <div class="mb-2">
                {{ admin ? 'Вы хотите отказаться от статуса админа?' : 'Вы хотите получить права админа?' }}
            </div>
            <div class="mb-3">
                <InputField
                    titleText="Введите пароль:"
                    type="password"
                    :errorsMessage="errorsPassword"
                    :isRequest="isRequest"
                    v-model="inputPassword"
                />
            </div>
        </template>
    </BaseModal>
</template>

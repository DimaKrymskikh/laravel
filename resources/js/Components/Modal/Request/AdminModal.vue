<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAdminModal, admin } = defineProps({
    hideAdminModal: Function,
    admin: Boolean
});

const app = inject('app');
const filmsAccount = inject('filmsAccount');

const inputPassword = ref('');
const errorsPassword = ref('');

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
            app.isRequest = true;
            errorsPassword.value = '';
        },
        onSuccess: () => {
            hideAdminModal();
        },
        onError: errors => {
            errorsPassword.value = errors.password;
        },
        onFinish: () => {
            app.isRequest = false;
        }
    });
};

</script>

<template>
    <BaseModal
        :headerTitle="admin ? 'Отказ от статуса админа' : 'Подтверждение статуса админа'"
        :hideModal="hideAdminModal"
        :handlerSubmit="handlerSubmit"
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
                    :isInputAutofocus="true"
                    v-model="inputPassword"
                />
            </div>
        </template>
    </BaseModal>
</template>

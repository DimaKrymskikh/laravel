<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import { app } from '@/Services/app';

const { hideAdminModal, admin } = defineProps({
    hideAdminModal: Function,
    admin: Boolean
});

const filmsAccount = inject('filmsAccount');

const inputPassword = ref('');
const errorsPassword = ref('');

const onBeforeForHandlerSubmit = () => {
            app.isRequest = true;
            errorsPassword.value = '';
        };

const onSuccessForHandlerSubmit = () => { hideAdminModal(); };

const onErrorForHandlerSubmit = errors => { errorsPassword.value = errors.password; };

const onFinishForHandlerSubmit = () => { app.isRequest = false; };

const handlerSubmit = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    let url = admin ? 'admin/destroy' : 'admin/create';
    
    router.post(filmsAccount.getUrl(url), {
            password: inputPassword.value
        }, {
            onBefore: onBeforeForHandlerSubmit,
            onSuccess: onSuccessForHandlerSubmit,
            onError: onErrorForHandlerSubmit,
            onFinish: onFinishForHandlerSubmit
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

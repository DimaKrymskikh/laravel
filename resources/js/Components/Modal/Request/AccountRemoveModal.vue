<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const { hideAccountRemoveModal } = defineProps({
    hideAccountRemoveModal: Function
});

const app = inject('app');

const inputPassword = ref('');
const errorsPassword = ref('');

// Обработчик удаления аккаунта
const handlerRemoveAccount = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete('register', {
        data: {
            password: inputPassword.value
        },
        onBefore: () => {
            app.isRequest = true;
            errorsPassword.value = '';
        },
        onSuccess: () => {
            hideAccountRemoveModal();
        },
        onError: errors => {
            errorsPassword.value = errors.password;
        },
        onFinish: () => {
            app.isRequest = false;
            inputPassword.value = '';
        }
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления аккаунта"
        :hideModal="hideAccountRemoveModal"
        :handlerSubmit="handlerRemoveAccount"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить свой аккаунт?
            </div>
            <form autocomplete="off">
                <div class="mb-3">
                    <InputField
                        titleText="Введите пароль:"
                        type="password"
                        :errorsMessage="errorsPassword"
                        :isInputAutofocus="true"
                        v-model="inputPassword"
                    />
                </div>
            </form>
        </template>
    </BaseModal>
</template>

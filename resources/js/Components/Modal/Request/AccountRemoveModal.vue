<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const { hideAccountRemoveModal } = defineProps({
    errors: Object,
    hideAccountRemoveModal: Function
});

const inputPassword = ref('');
const errorsPassword = ref('');
const isRequest = ref(false);

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
            isRequest.value = true;
            errorsPassword.value = '';
        },
        onSuccess: () => {
            hideAccountRemoveModal();
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
        modalId="account-remove-modal"
        headerTitle="Подтверждение удаления аккаунта"
        :hideModal="hideAccountRemoveModal"
        :handlerSubmit="handlerRemoveAccount"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить свой аккаунт?
            </div>
            <div class="mb-3">
                <label>
                    <InputField
                        titleText="Введите пароль:"
                        type="password"
                        :errorsMessage="errorsPassword"
                        :isRequest="isRequest"
                        v-model="inputPassword"
                    />
                </label>
            </div>
        </template>
    </BaseModal>
</template>

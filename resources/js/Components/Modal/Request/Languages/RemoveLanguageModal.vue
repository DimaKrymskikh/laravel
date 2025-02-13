<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    removeLanguage: Object,
    hideRemoveLanguageModal: Function
});

const app = inject('app');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveLanguage = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveLanguage = () => { props.hideRemoveLanguageModal(); };

const onErrorForHandlerRemoveLanguage = errors => {
            !!errors.password ? errorsPassword.value = errors.password : props.hideRemoveLanguageModal();
            app.errorRequest(errors);
        };

const onFinishForHandlerRemoveLanguage = () => { app.isRequest = false; };

/**
 * Обработчик удаления фильма
 * @param {Event} e
 * @returns {undefined}
 */
const handlerRemoveLanguage = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(`/admin/languages/${props.removeLanguage.id}`, {
        preserveScroll: true,
        data: {
            password: inputPassword.value,
        },
        onBefore: onBeforeForHandlerRemoveLanguage,
        onSuccess: onSuccessForHandlerRemoveLanguage,
        onError: onErrorForHandlerRemoveLanguage,
        onFinish: onFinishForHandlerRemoveLanguage
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления языка"
        :hideModal="hideRemoveLanguageModal"
        :handlerSubmit="handlerRemoveLanguage"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить <span>{{ removeLanguage.name }}</span> язык?
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

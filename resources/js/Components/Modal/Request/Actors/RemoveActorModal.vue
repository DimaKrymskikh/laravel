<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    removeActor: Object,
    hideRemoveActorModal: Function
});

const app = inject('app');
const actorsList = inject('actorsList');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveActor = () => {
            app.isRequest = true;
            errorsPassword.value = '';
        };

const onSuccessForHandlerRemoveActor = () => { props.hideRemoveActorModal(); };

const onErrorForHandlerRemoveActor = errors => { errorsPassword.value = errors.password; };

const onFinishForHandlerRemoveActor = () => { app.isRequest = false; };

/**
 * Обработчик удаления актёра
 * @param {Event} e
 * @returns {undefined}
 */
const handlerRemoveActor = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(actorsList.getUrl(props.removeActor.id), {
        preserveScroll: true,
        data: {
            password: inputPassword.value
        },
        onBefore: onBeforeForHandlerRemoveActor,
        onSuccess: onSuccessForHandlerRemoveActor,
        onError: onErrorForHandlerRemoveActor,
        onFinish: onFinishForHandlerRemoveActor
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления актёра"
        :hideModal="hideRemoveActorModal"
        :handlerSubmit="handlerRemoveActor"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить актёра
                <span>{{ removeActor.first_name }} {{ removeActor.last_name }}</span> ?
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

<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { actor } from '@/Services/Content/actors';
import { app } from '@/Services/app';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const actorsList = inject('actorsList');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const hideModal = function() {
    actor.hideRemoveActorModal();
};

const onBeforeForHandlerRemoveActor = () => {
            app.isRequest = true;
            errorsPassword.value = '';
        };

const onSuccessForHandlerRemoveActor = () => { actor.hideRemoveActorModal(); };

const onErrorForHandlerRemoveActor = errors => {
            !!errors.password ? errorsPassword.value = errors.password : actor.hideRemoveActorModal();
            app.errorRequest(errors);
        };

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
    
    router.delete(actorsList.getUrl(actor.id), {
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
        :hideModal="hideModal"
        :handlerSubmit="handlerRemoveActor"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить актёра
                <span>{{ actor.firstName }} {{ actor.lastName }}</span> ?
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

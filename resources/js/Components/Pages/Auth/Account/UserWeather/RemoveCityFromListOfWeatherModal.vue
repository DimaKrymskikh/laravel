<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import { app } from '@/Services/app';

const { removeCity, hideRemoveCityModal } = defineProps({
    removeCity: Object,
    hideRemoveCityModal: Function
});

// Значение поля пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveCity = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveCity = () => { hideRemoveCityModal(); };

const onErrorForHandlerRemoveCity = errors => { errorsPassword.value = errors.password; };

const onFinishForHandlerRemoveCity = () => { app.isRequest = false; };

/**
 * Обработчик удаления фильма
 * @param {Event} e
 * @returns {undefined}
 */
const handlerRemoveCity = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(`cities/removecity/${removeCity.id}`, {
        preserveScroll: true,
        data: {
            password: inputPassword.value
        },
        onBefore: onBeforeForHandlerRemoveCity,
        onSuccess: onSuccessForHandlerRemoveCity,
        onError: onErrorForHandlerRemoveCity,
        onFinish: onFinishForHandlerRemoveCity
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления города из просмотра погоды"
        :hideModal="hideRemoveCityModal"
        :handlerSubmit="handlerRemoveCity"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить город
                <span>{{ removeCity.name }}</span>?
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

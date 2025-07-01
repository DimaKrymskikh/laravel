<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const hideModal = function() {
    city.hideRemoveCityModal();
};

const onBeforeForHandlerRemoveCity = () => {
            app.isRequest = true;
            errorsPassword.value = '';
        };

const onSuccessForHandlerRemoveCity = () => { city.hideRemoveCityModal(); };

const onErrorForHandlerRemoveCity = errors => {
            !!errors.password ? errorsPassword.value = errors.password : city.hideRemoveCityModal();
            app.errorRequest(errors);
        };

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
    
    router.delete(`cities/${city.id}`, {
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
        headerTitle="Подтверждение удаления города"
        :hideModal="hideModal"
        :handlerSubmit="handlerRemoveCity"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить город
                <span>{{ city.name }}</span> [<span class="font-sans">{{ city.openWeatherId }}</span>] ?
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

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { removeCity, hideRemoveCityModal } = defineProps({
    removeCity: Object,
    hideRemoveCityModal: Function
});

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');
// Выполняется ли запрос на сервер
const isRequest = ref(false);

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
    
    router.delete(`cities/${removeCity.id}`, {
        preserveScroll: true,
        data: {
            password: inputPassword.value,
        },
        onBefore: () => {
            isRequest.value = true;
            errorsPassword.value = '';
        },
        onSuccess: () => {
            hideRemoveCityModal();
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
        modalId="remove-city-modal"
        headerTitle="Подтверждение удаления города"
        :hideModal="hideRemoveCityModal"
        :handlerSubmit="handlerRemoveCity"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить город
                <span>{{ removeCity.name }}</span> [<span class="font-sans">{{ removeCity.open_weather_id }}</span>] ?
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

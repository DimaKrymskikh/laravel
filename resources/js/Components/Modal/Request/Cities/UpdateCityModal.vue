<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { updateCity, hideUpdateCityModal } = defineProps({
    updateCity: Object,
    hideUpdateCityModal: Function
});

const app = inject('app');

const cityName = ref(updateCity.name);
const errorsName = ref('');

const onBeforeForHandlerUpdateCity = () => {
            app.isRequest = true;
            errorsName.value = '';
        };

const onSuccessForHandlerUpdateCity = () => { hideUpdateCityModal(); };

const onErrorForHandlerUpdateCity = errors => { errorsName.value = errors.name; };

const onFinishForHandlerUpdateCity = () => { app.isRequest = false; };

/**
 * Обработчик изменения названия города
 * @param {Event} e
 * @returns {undefined}
 */
const handlerUpdateCity = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(`cities/${updateCity.id}`, {
        name: cityName.value
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerUpdateCity,
        onSuccess: onSuccessForHandlerUpdateCity,
        onError: onErrorForHandlerUpdateCity,
        onFinish: onFinishForHandlerUpdateCity
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Изменение названия города"
        :hideModal="hideUpdateCityModal"
        :handlerSubmit="handlerUpdateCity"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Имя города:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isInputAutofocus="true"
                    v-model="cityName"
                />
            </div>
        </template>
    </BaseModal>
</template>

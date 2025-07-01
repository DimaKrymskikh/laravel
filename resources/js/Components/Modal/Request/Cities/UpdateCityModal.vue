<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const cityName = ref(city.name);
const errorsName = ref('');

const hideModal = function() {
    city.hideUpdateCityModal();
};

const onBeforeForHandlerUpdateCity = () => {
            app.isRequest = true;
            errorsName.value = '';
        };

const onSuccessForHandlerUpdateCity = () => { city.hideUpdateCityModal(); };

const onErrorForHandlerUpdateCity = errors => {
            errorsName.value = errors.name;
            app.errorRequest(errors);
            if(errors.message) {
                city.hideUpdateCityModal();
            }
        };

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
    
    router.put(`cities/${city.id}`, {
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
        :hideModal="hideModal"
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

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const cityName = ref(city.name);
const openWeatherId = ref(city.openWeatherId);
const errorsName = ref('');
const errorsOpenWeatherId = ref('');

const hideModal = function() {
    city.hideAddCityModal();
};

const onBeforeForHandlerAddCity = () => {
            app.isRequest = true;
            errorsName.value = '';
            errorsOpenWeatherId.value = '';
        };

const onSuccessForHandlerAddCity = () => { city.hideAddCityModal(); };

const onErrorForHandlerAddCity = errors => {
            errorsName.value = errors.name;
            errorsOpenWeatherId.value = errors.open_weather_id;
        };

const onFinishForHandlerAddCity = () => { app.isRequest = false; };

const handlerAddCity = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/cities', {
            name: cityName.value,
            open_weather_id: openWeatherId.value
        }, {
        onBefore: onBeforeForHandlerAddCity,
        onSuccess: onSuccessForHandlerAddCity,
        onError: onErrorForHandlerAddCity,
        onFinish: onFinishForHandlerAddCity
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление города"
        :hideModal="hideModal"
        :handlerSubmit="handlerAddCity"
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
                <InputField
                    titleText="Id города в OpenWeather:"
                    type="text"
                    :errorsMessage="errorsOpenWeatherId"
                    v-model="openWeatherId"
                />
            </div>
        </template>
    </BaseModal>
</template>

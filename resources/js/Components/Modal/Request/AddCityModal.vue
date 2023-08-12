<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAddCityModal } = defineProps({
    hideAddCityModal: Function
});

const cityName = ref('');
const openWeatherId = ref('');
const errorsName = ref('');
const errorsOpenWeatherId = ref('');

const isRequest = ref(false);

const handlerAddCity = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/cities', {
            name: cityName.value,
            open_weather_id: openWeatherId.value
        }, {
        onBefore: () => {
            isRequest.value = true;
            errorsName.value = '';
            errorsOpenWeatherId.value = '';
        },
        onSuccess: () => {
            hideAddCityModal();
        },
        onError: errors => {
            errorsName.value = errors.name;
            errorsOpenWeatherId.value = errors.open_weather_id;
        },
        onFinish: () => {
            isRequest.value = false;
        }
    });
};

</script>

<template>
    <BaseModal
        modalId="add-city-modal"
        headerTitle="Добавление города"
        :hideModal="hideAddCityModal"
        :handlerSubmit="handlerAddCity"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Имя города:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isRequest="isRequest"
                    v-model="cityName"
                />
                <InputField
                    titleText="Id города в OpenWeather:"
                    type="text"
                    :errorsMessage="errorsOpenWeatherId"
                    :isRequest="isRequest"
                    v-model="openWeatherId"
                />
            </div>
        </template>
    </BaseModal>
</template>

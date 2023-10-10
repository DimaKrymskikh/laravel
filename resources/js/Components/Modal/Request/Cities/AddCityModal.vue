<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAddCityModal } = defineProps({
    hideAddCityModal: Function
});

const app = inject('app');

const cityName = ref('');
const openWeatherId = ref('');
const errorsName = ref('');
const errorsOpenWeatherId = ref('');

const handlerAddCity = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/cities', {
            name: cityName.value,
            open_weather_id: openWeatherId.value
        }, {
        onBefore: () => {
            app.isRequest = true;
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
            app.isRequest = false;
        }
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление города"
        :hideModal="hideAddCityModal"
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

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { updateCity, hideUpdateCityModal } = defineProps({
    updateCity: Object,
    hideUpdateCityModal: Function
});

const cityName = ref(updateCity.name);
const errorsName = ref('');
// Выполняется ли запрос на сервер
const isRequest = ref(false);

/**
 * Обработчик удаления фильма
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
        onBefore: () => {
            isRequest.value = true;
            errorsName.value = '';
        },
        onSuccess: () => {
            hideUpdateCityModal();
        },
        onError: errors => {
            errorsName.value = errors.name;
        },
        onFinish: () => {
            isRequest.value = false;
        }
    });
};
</script>

<template>
    <BaseModal
        modalId="update-city-modal"
        headerTitle="Изменение названия города"
        :hideModal="hideUpdateCityModal"
        :handlerSubmit="handlerUpdateCity"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Название города:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isRequest="isRequest"
                    v-model="cityName"
                />
            </div>
        </template>
    </BaseModal>
</template>

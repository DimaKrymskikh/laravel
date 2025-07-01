<script setup>
import { ref, watch, inject } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const headerTitle = `Изменение временного пояса города ${city.name}`;

const cityTimeZone = ref(city.timeZone);

const errorsName = ref('');

const timezones = ref(null);

const hideModal = function() {
    city.hideUpdateTimeZoneModal();
};

const handlerTimeZoneName = async function() {
    if(cityTimeZone.value.length < 3) {
        return;
    }
    
    timezones.value = await app.request(`/admin/timezone?name=${cityTimeZone.value}`, 'GET');
};

const onBeforeForHandlerUpdateTimeZone = () => {
            app.isRequest = true;
            errorsName.value = '';
        };

const onSuccessForHandlerUpdateTimeZone = () => { city.hideUpdateTimeZoneModal(); };

const onErrorForHandlerUpdateTimeZone = errors => {
            errorsName.value = errors.name;
        };

const onFinishForHandlerUpdateTimeZone = () => { app.isRequest = false; };

/**
 * Обработчик изменения временного пояса города
 * @param {Event} e
 * @returns {undefined}
 */
const handlerUpdateTimeZone = function(e) {
    // Защита от повторного запроса
    if(e.target.classList.contains('disabled')) {
        return;
    }
    
    const timezone_id = e.target.getAttribute('data-id');
    
    router.put(`cities/${city.id}/timezone/${timezone_id}`, {
        timezone_id: cityTimeZone.value
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerUpdateTimeZone,
        onSuccess: onSuccessForHandlerUpdateTimeZone,
        onError: onErrorForHandlerUpdateTimeZone,
        onFinish: onFinishForHandlerUpdateTimeZone
    });
};

watch(cityTimeZone, handlerTimeZoneName);
</script>

<template>
    <BaseModal
        :headerTitle=headerTitle
        :hideModal="hideModal"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Временной пояс города:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isInputAutofocus="true"
                    v-model="cityTimeZone"
                />
                <div v-if="!timezones">
                    Введите хотя бы три символа для получения временных поясов
                </div>
                <div v-if="timezones && !timezones.length">
                    Ничего не найдено
                </div>
                <ul 
                    class="overflow-x-hidden overflow-y-auto h-48"
                    @click="handlerUpdateTimeZone"
                    v-if="timezones && timezones.length"
                >
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="0"
                    >
                        Убрать временной пояс
                    </li>
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="tz.id"
                        v-for="tz in timezones"
                    >
                        {{ tz.name }}
                    </li>
                </ul>
            </div>
        </template>
    </BaseModal>
</template>


<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    updateLanguage: Object,
    hideUpdateLanguageModal: Function
});

const app = inject('app');

const languageName = ref(props.updateLanguage.name);
const errorsName = ref('');

const onBeforeForHandlerUpdateLanguage = () => {
    app.isRequest = true;
    errorsName.value = '';
};

const onSuccessForHandlerUpdateLanguage = () => { props.hideUpdateLanguageModal(); };

const onErrorForHandlerUpdateLanguage = errors => {
    errorsName.value = errors.name;
    app.errorRequest(errors);
    if(errors.message) {
        props.hideUpdateLanguageModal();
    }
};

const onFinishForHandlerUpdateLanguage = () => { app.isRequest = false; };

/**
 * Обработчик изменения названия города
 * @param {Event} e
 * @returns {undefined}
 */
const handlerUpdateLanguage = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(`/admin/languages/${props.updateLanguage.id}`, {
        name: languageName.value
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerUpdateLanguage,
        onSuccess: onSuccessForHandlerUpdateLanguage,
        onError: onErrorForHandlerUpdateLanguage,
        onFinish: onFinishForHandlerUpdateLanguage
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Изменение названия языка"
        :hideModal="hideUpdateLanguageModal"
        :handlerSubmit="handlerUpdateLanguage"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Имя языка:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isInputAutofocus="true"
                    v-model="languageName"
                />
            </div>
        </template>
    </BaseModal>
</template>

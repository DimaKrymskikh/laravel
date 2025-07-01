<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { language } from '@/Services/Content/languages';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const languageName = ref(language.name);
const errorsName = ref('');

const hideModal = function() {
    language.hideAddLanguageModal();
};

const onBeforeForHandlerAddLanguage = () => {
    app.isRequest = true;
    errorsName.value = '';
};

const onSuccessForHandlerAddLanguage = () => { language.hideAddLanguageModal(); };

const onErrorForHandlerAddLanguage = errors => { errorsName.value = errors.name; };

const onFinishForHandlerAddLanguage = () => { app.isRequest = false; };

const handlerAddLanguage = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/languages', {
            name: languageName.value
        }, {
        onBefore: onBeforeForHandlerAddLanguage,
        onSuccess: onSuccessForHandlerAddLanguage,
        onError: onErrorForHandlerAddLanguage,
        onFinish: onFinishForHandlerAddLanguage
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление языка"
        :hideModal="hideModal"
        :handlerSubmit="handlerAddLanguage"
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

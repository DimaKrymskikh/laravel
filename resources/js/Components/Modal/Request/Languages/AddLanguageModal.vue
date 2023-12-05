<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAddLanguageModal } = defineProps({
    hideAddLanguageModal: Function
});

const app = inject('app');

const languageName = ref('');
const errorsName = ref('');

const handlerAddLanguage = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/languages', {
            name: languageName.value
        }, {
        onBefore: () => {
            app.isRequest = true;
            errorsName.value = '';
        },
        onSuccess: () => {
            hideAddLanguageModal();
        },
        onError: errors => {
            errorsName.value = errors.name;
        },
        onFinish: () => {
            app.isRequest = false;
        }
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление языка"
        :hideModal="hideAddLanguageModal"
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

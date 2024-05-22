<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    updateActor: Object,
    hideUpdateActorModal: Function
});

const app = inject('app');
const actorsList = inject('actorsList');

const actorFirstName = ref(props.updateActor.first_name);
const actorLastName = ref(props.updateActor.last_name);
const errorsFirstName = ref('');
const errorsLastName = ref('');

/**
 * Обработчик изменения полного имени актёра
 * @param {Event} e
 * @returns {undefined}
 */
const handlerUpdateActor = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(actorsList.getUrl(props.updateActor.id), {
        first_name: actorFirstName.value,
        last_name: actorLastName.value
    }, {
        preserveScroll: true,
        onBefore: () => {
            app.isRequest = true;
            errorsFirstName.value = '';
            errorsLastName.value = '';
        },
        onSuccess: res => {
            // Если было изменено имя или фамилия актёра, то текущая страница пагинации может измениться
            actorsList.page = res.props.actors.current_page;
            props.hideUpdateActorModal();
        },
        onError: errors => {
            errorsFirstName.value = errors.first_name;
            errorsLastName.value = errors.last_name;
        },
        onFinish: () => {
            app.isRequest = false;
        }
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Изменение полного имени актёра"
        :hideModal="hideUpdateActorModal"
        :handlerSubmit="handlerUpdateActor"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Имя актёра:"
                    type="text"
                    :errorsMessage="errorsFirstName"
                    :isInputAutofocus="true"
                    v-model="actorFirstName"
                />
                <InputField
                    titleText="Фамилия актёра:"
                    type="text"
                    :errorsMessage="errorsLastName"
                    v-model="actorLastName"
                />
            </div>
        </template>
    </BaseModal>
</template>

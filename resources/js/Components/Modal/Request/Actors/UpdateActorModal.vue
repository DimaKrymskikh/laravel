<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { actor } from '@/Services/Content/actors';
import { app } from '@/Services/app';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const actorsList = inject('actorsList');

const actorFirstName = ref(actor.firstName);
const actorLastName = ref(actor.lastName);
const errorsFirstName = ref('');
const errorsLastName = ref('');

const hideModal = function() {
    actor.hideUpdateActorModal();
};

const onBeforeForHandlerUpdateActor = () => {
            app.isRequest = true;
            errorsFirstName.value = '';
            errorsLastName.value = '';
        };

const onSuccessForHandlerUpdateActor = res => {
            // Если было изменено имя или фамилия актёра, то текущая страница пагинации может измениться
            actorsList.page = res.props.actors.current_page;
            // При изменении актёра сбрасываем фильтр поиска
            actorsList.name = '';
            actor.hideUpdateActorModal();
        };

const onErrorForHandlerUpdateActor = errors => {
            errorsFirstName.value = errors.first_name;
            errorsLastName.value = errors.last_name;
            app.errorRequest(errors);
            if(errors.message) {
                actor.hideUpdateActorModal();
            }
        };

const onFinishForHandlerUpdateActor = () => { app.isRequest = false; };

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
    
    router.put(actorsList.getUrl(actor.id), {
        first_name: actorFirstName.value,
        last_name: actorLastName.value
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerUpdateActor,
        onSuccess: onSuccessForHandlerUpdateActor,
        onError: onErrorForHandlerUpdateActor,
        onFinish: onFinishForHandlerUpdateActor
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Изменение полного имени актёра"
        :hideModal="hideModal"
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

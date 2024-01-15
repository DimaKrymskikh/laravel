<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { hideAddActorModal } = defineProps({
    hideAddActorModal: Function
});

const app = inject('app');
const actorsList = inject('actorsList');

const firstName = ref('');
const lastName = ref('');
const errorsFirstName = ref('');
const errorsLastName = ref('');

const handlerAddActor = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post(actorsList.getUrl(), {
            first_name: firstName.value,
            last_name: lastName.value
        }, {
        onBefore: () => {
            app.isRequest = true;
            errorsFirstName.value = '';
            errorsLastName.value = '';
        },
        onSuccess: (res) => {
            hideAddActorModal();
            // При добавлении актёра сбрасываем фильтр поиска
            actorsList.name = '';
            // Запоминаем активную страницу пагинации
            actorsList.page = res.props.actors.current_page;
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
        headerTitle="Добавление актёра"
        :hideModal="hideAddActorModal"
        :handlerSubmit="handlerAddActor"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Имя актёра:"
                    type="text"
                    :errorsMessage="errorsFirstName"
                    :isInputAutofocus="true"
                    v-model="firstName"
                />
                <InputField
                    titleText="Фамилия актёра:"
                    type="text"
                    :errorsMessage="errorsLastName"
                    v-model="lastName"
                />
            </div>
        </template>
    </BaseModal>
</template>

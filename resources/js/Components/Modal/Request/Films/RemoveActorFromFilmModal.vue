<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    updateFilm: Object,
    removeActor: Object,
    hideRemoveActorFromFilmModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveActorFromFilm = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveActorFromFilm = () => { props.hideRemoveActorFromFilmModal(); };

const onErrorForHandlerRemoveActorFromFilm = errors => { errorsPassword.value = errors.password; };

const onFinishForHandlerRemoveActorFromFilm = () => { app.isRequest = false; };

const handlerRemoveActorFromFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(filmsAdmin.getUrl(`/admin/films/actors/${props.removeActor.id}`), {
        preserveScroll: true,
        data: {
            film_id: props.updateFilm.id,
            password: inputPassword.value
        },
        onBefore: onBeforeForHandlerRemoveActorFromFilm,
        onSuccess: onSuccessForHandlerRemoveActorFromFilm,
        onError: onErrorForHandlerRemoveActorFromFilm,
        onFinish: onFinishForHandlerRemoveActorFromFilm
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления актёра из фильма"
        :hideModal="hideRemoveActorFromFilmModal"
        :handlerSubmit="handlerRemoveActorFromFilm"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить актёра
                <span>{{ removeActor.first_name }} {{ removeActor.last_name }}</span>
                из фильма <span>{{ updateFilm.title }}</span>?
            </div>
            <div class="mb-3">
                <InputField
                    titleText="Введите пароль:"
                    type="password"
                    :errorsMessage="errorsPassword"
                    :isInputAutofocus="true"
                    v-model="inputPassword"
                />
            </div>
        </template>
    </BaseModal>
</template>

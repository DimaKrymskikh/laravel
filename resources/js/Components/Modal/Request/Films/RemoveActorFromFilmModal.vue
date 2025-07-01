<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { film, removeActor } from '@/Services/Content/films';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const filmsAdmin = inject('filmsAdmin');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const hideModal = function() {
    removeActor.hideRemoveActorFromFilmModal();
}

const onBeforeForHandlerRemoveActorFromFilm = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveActorFromFilm = () => { removeActor.hideRemoveActorFromFilmModal(); };

const onErrorForHandlerRemoveActorFromFilm = errors => { errorsPassword.value = errors.password; };

const onFinishForHandlerRemoveActorFromFilm = () => { app.isRequest = false; };

const handlerRemoveActorFromFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(filmsAdmin.getUrl(`/admin/films/actors/${removeActor.id}`), {
        preserveScroll: true,
        data: {
            film_id: film.id,
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
        :hideModal="hideModal"
        :handlerSubmit="handlerRemoveActorFromFilm"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить актёра
                <span>{{ removeActor.firstName }} {{ removeActor.lastName }}</span>
                из фильма <span>{{ film.title }}</span>?
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

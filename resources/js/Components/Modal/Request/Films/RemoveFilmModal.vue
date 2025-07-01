<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
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
    film.hideRemoveFilmModal();
};

const onBeforeForHandlerRemoveFilm = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveFilm = () => { film.hideRemoveFilmModal(); };

const onErrorForHandlerRemoveFilm = errors => {
            errors.password ? errorsPassword.value = errors.password : film.hideRemoveFilmModal();
            app.errorRequest(errors);
        };

const onFinishForHandlerRemoveFilm = () => { app.isRequest = false; };

/**
 * Обработчик удаления актёра
 * @param {Event} e
 * @returns {undefined}
 */
const handlerRemoveFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(filmsAdmin.getUrl(`/admin/films/${film.id}`), {
        preserveScroll: true,
        data: {
            password: inputPassword.value
        },
        onBefore: onBeforeForHandlerRemoveFilm,
        onSuccess: onSuccessForHandlerRemoveFilm,
        onError: onErrorForHandlerRemoveFilm,
        onFinish: onFinishForHandlerRemoveFilm
    });
};
</script>

<template>
    <BaseModal
        headerTitle="Подтверждение удаления фильма"
        :hideModal="hideModal"
        :handlerSubmit="handlerRemoveFilm"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить фильм
                <span>{{ film.title }}</span> ?
            </div>
            <form @submit.prevent autocomplete="off">
                <div class="mb-3">
                    <InputField
                        titleText="Введите пароль:"
                        type="password"
                        :errorsMessage="errorsPassword"
                        :isInputAutofocus="true"
                        v-model="inputPassword"
                    />
                </div>
            </form>
        </template>
    </BaseModal>
</template>

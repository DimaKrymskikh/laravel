<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    removeFilm: Object,
    hideRemoveFilmModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveFilm = () => {
    app.isRequest = true;
    errorsPassword.value = '';
};

const onSuccessForHandlerRemoveFilm = () => { props.hideRemoveFilmModal(); };

const onErrorForHandlerRemoveFilm = errors => { errorsPassword.value = errors.password; };

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
    
    router.delete(filmsAdmin.getUrl(`/admin/films/${props.removeFilm.id}`), {
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
        :hideModal="hideRemoveFilmModal"
        :handlerSubmit="handlerRemoveFilm"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить фильм
                <span>{{ removeFilm.title }}</span> ?
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

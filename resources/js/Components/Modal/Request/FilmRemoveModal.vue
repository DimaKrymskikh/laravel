<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const { films, removeFilmId, hideFilmRemoveModal } = defineProps({
    films: Object,
    removeFilmTitle: String,
    removeFilmId: String,
    hideFilmRemoveModal: Function
});

const app = inject('app');
const filmsAccount = inject('filmsAccount');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке,
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');

const onBeforeForHandlerRemoveFilm = () => {
            app.isRequest = true;
            errorsPassword.value = '';
        };

const onSuccessForHandlerRemoveFilm = res => {
            hideFilmRemoveModal();
            filmsAccount.page = res.props.films.current_page;
        };

const onErrorForHandlerRemoveFilm = errors => {
            errors.password ? errorsPassword.value = errors.password : hideFilmRemoveModal();
            app.errorRequest(errors);
        };

const onFinishForHandlerRemoveFilm = () => {
            app.isRequest = false;
        };

/**
 * Обработчик удаления фильма
 * @param {Event} e
 * @returns {undefined}
 */
const handlerRemoveFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(filmsAccount.getUrl(`userfilms/removefilm/${removeFilmId}`), {
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
        :hideModal="hideFilmRemoveModal"
        :handlerSubmit="handlerRemoveFilm"
    >
        <template v-slot:body>
            <div class="mb-2">
                Вы действительно хотите удалить фильм 
                <span>{{ removeFilmTitle }}</span>?
            </div>
            <form @submit.prevent autocomplete="off">
                <div class="mb-3">
                    <InputField
                        titleText="Введите пароль:"
                        type="password"
                        :isInputAutofocus="true"
                        :errorsMessage="errorsPassword"
                        v-model="inputPassword"
                    />
                </div>
            </form>
        </template>
    </BaseModal>
</template>

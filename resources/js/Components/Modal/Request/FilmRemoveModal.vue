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
    
    router.delete(`userfilms/removefilm/${removeFilmId}`, {
        preserveScroll: true,
        data: {
            password: inputPassword.value,
            page: filmsAccount.page,
            number: filmsAccount.perPage,
            title: filmsAccount.title,
            description: filmsAccount.description
        },
        onBefore: () => {
            app.isRequest = true;
            errorsPassword.value = '';
        },
        onSuccess: response => {
            hideFilmRemoveModal();
            filmsAccount.page = response.props.films.current_page;
        },
        onError: errors => {
            errorsPassword.value = errors.password;
        },
        onFinish: () => {
            app.isRequest = false;
        }
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
            <form autocomplete="off">
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

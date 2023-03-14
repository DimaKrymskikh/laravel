<script setup>
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/BaseModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { getPageAfterRemoveItem } from '@/Tools/Paginate';

const { films, removeFilmId, hideFilmRemoveModal } = defineProps({
    films: Object,
    errors: Object,
    removeFilmTitle: String,
    removeFilmId: String,
    hideFilmRemoveModal: Function
});

const filmsAccount = inject('filmsAccount');

// Величина поля для пароля
const inputPassword = ref('');
// Сообщение об ошибке ввода пароля.
// При монтировании компоненты - пустая строка, чтобы после закрытия модального окна с сообщением об ошибке,
// при последующем открытии модального окна этого сообщения об ошибке не было.
const errorsPassword = ref('');
// Выполняется ли запрос на сервер
const isRequest = ref(false);

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

    filmsAccount.page = getPageAfterRemoveItem(films);
    
    router.delete(`account/removefilm/${removeFilmId}`, {
        preserveScroll: true,
        data: {
            password: inputPassword.value,
            page: filmsAccount.page,
            number: filmsAccount.perPage,
            title: filmsAccount.title,
            description: filmsAccount.description
        },
        onBefore: () => {
            isRequest.value = true;
        },
        onSuccess: () => {
            hideFilmRemoveModal();
        },
        onError: errors => {
            errorsPassword.value = errors.password;
        },
        onFinish: () => {
            isRequest.value = false;
        }
    });
};
</script>

<template>
    <BaseModal
        modalId="film-remove-modal"
        headerTitle="Подтверждение удаления фильма"
        :hideModal="hideFilmRemoveModal"
        :handlerSubmit="handlerRemoveFilm"
        :isRequest="isRequest"
    >
        <template v-slot:body>
            Вы действительно хотите удалить фильм 
            <span>{{ removeFilmTitle }}</span>?
            <div class="mb-3">
                <Spinner hSpinner="h-8" v-if="isRequest" />
                <InputField v-else
                    titleText="Введите пароль:"
                    type="password"
                    :errorsMessage="errorsPassword"
                    v-model="inputPassword"
                />
            </div>
        </template>
    </BaseModal>
</template>

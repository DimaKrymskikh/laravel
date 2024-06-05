<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    hideAddFilmModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

const title = ref('');
const description = ref('');
const errorsTitle = ref('');
const errorsDescription = ref('');

const onBeforeForHandlerAddFilm = () => {
    app.isRequest = true;
    errorsTitle.value = '';
    errorsDescription.value = '';
};

const onSuccessForHandlerAddFilm = res => { 
            props.hideAddFilmModal();
            // При добавлении фильма сбрасываем фильтр поиска
            filmsAdmin.resetSearchFilter();
            // Запоминаем активную страницу пагинации
            filmsAdmin.page = res.props.films.current_page;
        };

const onErrorForHandlerAddFilm = errors => {
            errorsTitle.value = errors.title;
            errorsDescription.value = errors.description;
        };

const onFinishForHandlerAddFilm = () => { app.isRequest = false; };

const handlerAddFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post(filmsAdmin.getUrl('/admin/films'), {
            title: title.value,
            description: description.value
        }, {
        onBefore: onBeforeForHandlerAddFilm,
        onSuccess: onSuccessForHandlerAddFilm,
        onError: onErrorForHandlerAddFilm,
        onFinish: onFinishForHandlerAddFilm
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление фильма"
        :hideModal="hideAddFilmModal"
        :handlerSubmit="handlerAddFilm"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Название фильма:"
                    type="text"
                    :errorsMessage="errorsTitle"
                    :isInputAutofocus="true"
                    v-model="title"
                />
                <InputField
                    titleText="Описание фильма:"
                    type="text"
                    :errorsMessage="errorsDescription"
                    v-model="description"
                />
            </div>
        </template>
    </BaseModal>
</template>

<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const filmsAdmin = inject('filmsAdmin');

const title = ref(film.title);
const description = ref(film.description);
const errorsTitle = ref('');
const errorsDescription = ref('');

const hideModal = function() {
    film.hideAddFilmModal();
};

const onBeforeForHandlerAddFilm = () => {
    app.isRequest = true;
    errorsTitle.value = '';
    errorsDescription.value = '';
};

const onSuccessForHandlerAddFilm = res => { 
            film.hideAddFilmModal();
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
        :hideModal="hideModal"
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

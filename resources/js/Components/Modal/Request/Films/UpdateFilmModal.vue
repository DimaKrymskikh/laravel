<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const filmsAdmin = inject('filmsAdmin');

const fieldValue = ref(film.fieldValue);
const errorsField = ref('');

const hideModal = function() {
    film.hideUpdateFilmModal();
};

const onBeforeForHandlerUpdateFilm = () => {
    app.isRequest = true;
    errorsField.value = '';
};

const onSuccessForHandlerUpdateFilm = res => {
    // Если было изменено название фильма, то текущая страница пагинации может измениться
    filmsAdmin.page = res.props.films.current_page;
    film.hideUpdateFilmModal();
};

const onErrorForHandlerUpdateFilm = errors => {
    errorsField.value = errors[film.field];
    app.errorRequest(errors);
    if(errors.message) {
        film.hideUpdateFilmModal();
    }
};

const onFinishForHandlerUpdateFilm = () => { app.isRequest = false; };

const handlerUpdateFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(filmsAdmin.getUrl(`/admin/films/${film.id}`), {
            field: film.field,
            [film.field]: fieldValue.value
        }, {
        onBefore: onBeforeForHandlerUpdateFilm,
        onSuccess: onSuccessForHandlerUpdateFilm,
        onError: onErrorForHandlerUpdateFilm,
        onFinish: onFinishForHandlerUpdateFilm
    });
};

let headerTitle = '';
let titleText = '';

switch(film.field) {
    case 'title': 
        headerTitle = `Изменение названия фильма ${film.title}`;
        titleText = 'Название фильма:';
        break;
    case 'description': 
        headerTitle = `Изменение описания фильма ${film.title}`;
        titleText = 'Описание фильма:';
        break;
    case 'release_year': 
        headerTitle = `Изменение года выхода фильма ${film.title}`;
        titleText = 'Год выхода фильма:';
        break;
}

</script>

<template>
    <BaseModal
        :headerTitle="headerTitle"
        :hideModal="hideModal"
        :handlerSubmit="handlerUpdateFilm"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    :titleText="titleText"
                    type="text"
                    :errorsMessage="errorsField"
                    :isInputAutofocus="true"
                    v-model="fieldValue"
                />
            </div>
        </template>
    </BaseModal>
</template>

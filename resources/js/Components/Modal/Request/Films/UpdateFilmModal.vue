<script setup lang="ts">
import { inject, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import { updateFilm } from '@/Services/films';

const props = defineProps({
    hideUpdateFilmModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

const fieldValue = ref(updateFilm.fieldValue);
const errorsField = ref('');

const onBeforeForHandlerUpdateFilm = () => {
    app.isRequest = true;
    errorsField.value = '';
};

const onSuccessForHandlerUpdateFilm = res => {
    // Если было изменено название фильма, то текущая страница пагинации может измениться
    filmsAdmin.page = res.props.films.current_page;
    props.hideUpdateFilmModal();
};

const onErrorForHandlerUpdateFilm = errors => {
    errorsField.value = errors[updateFilm.field];
    app.errorRequest(errors);
    if(errors.message) {
        props.hideUpdateFilmModal();
    }
};

const onFinishForHandlerUpdateFilm = () => { app.isRequest = false; };

const handlerUpdateFilm = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(filmsAdmin.getUrl(`/admin/films/${updateFilm.id}`), {
            field: updateFilm.field,
            [updateFilm.field]: fieldValue.value
        }, {
        onBefore: onBeforeForHandlerUpdateFilm,
        onSuccess: onSuccessForHandlerUpdateFilm,
        onError: onErrorForHandlerUpdateFilm,
        onFinish: onFinishForHandlerUpdateFilm
    });
};

let headerTitle = '';
let titleText = '';

switch(updateFilm.field) {
    case 'title': 
        headerTitle = `Изменение названия фильма ${updateFilm.title}`;
        titleText = 'Название фильма:';
        break;
    case 'description': 
        headerTitle = `Изменение описания фильма ${updateFilm.title}`;
        titleText = 'Описание фильма:';
        break;
    case 'release_year': 
        headerTitle = `Изменение года выхода фильма ${updateFilm.title}`;
        titleText = 'Год выхода фильма:';
        break;
}

</script>

<template>
    <BaseModal
        :headerTitle="headerTitle"
        :hideModal="hideUpdateFilmModal"
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

<script setup>
import { inject, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const props = defineProps({
    updateFilm: Object,
    hideUpdateFilmLanguageModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

const headerTitle = `Изменение языка фильма ${props.updateFilm.title}`;

const filmLanguage = ref('');

const errorsName = ref('');

const languages = ref(null);

const handlerLanguageName = async function() {
    languages.value = await app.request(`/admin/languages/getJson?name_filter=${filmLanguage.value}`, 'GET');
};
handlerLanguageName();

const onBeforeForHandlerUpdateFilmLanguage = () => {
    app.isRequest = true;
    errorsName.value = '';
};

const onSuccessForHandlerUpdateFilmLanguage = () => { props.hideUpdateFilmLanguageModal(); };

const onErrorForHandlerUpdateFilmLanguage = errors => { errorsName.value = errors.name; };

const onFinishForHandlerUpdateFilmLanguage = () => { app.isRequest = false; };

const handlerUpdateFilmLanguage = function(e) {
    // Защита от повторного запроса
    if(e.target.classList.contains('disabled')) {
        return;
    }
    
    const language_id = e.target.getAttribute('data-id');
    
    router.put(filmsAdmin.getUrl(`/admin/films/${props.updateFilm.id}`), {
        field: 'language_id',
        language_id
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerUpdateFilmLanguage,
        onSuccess: onSuccessForHandlerUpdateFilmLanguage,
        onError: onErrorForHandlerUpdateFilmLanguage,
        onFinish: onFinishForHandlerUpdateFilmLanguage
    });
};

watch(filmLanguage, handlerLanguageName);
</script>

<template>
    <BaseModal
        :headerTitle=headerTitle
        :hideModal="hideUpdateFilmLanguageModal"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Язык фильма:"
                    type="text"
                    :errorsMessage="errorsName"
                    :isInputAutofocus="true"
                    v-model="filmLanguage"
                />
                <div v-if="languages && !languages.length">
                    Ничего не найдено
                </div>
                <ul 
                    class="overflow-x-hidden overflow-y-auto h-48"
                    @click="handlerUpdateFilmLanguage"
                    v-if="languages && languages.length"
                >
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="null"
                    >
                        Убрать язык фильма
                    </li>
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="lang.id"
                        v-for="lang in languages"
                    >
                        {{ lang.name }}
                    </li>
                </ul>
            </div>
        </template>
    </BaseModal>
</template>

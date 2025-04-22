<script setup>
import { inject, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const props = defineProps({
    updateFilm: Object,
    hideUpdateFilmActorsModal: Function,
    showRemoveActorFromFilmModal: Function
});

const app = inject('app');
const filmsAdmin = inject('filmsAdmin');

const headerTitle = `Изменение списка актёров фильма ${props.updateFilm.title}`;

const actorName = ref('');
const filmActors = ref(null);
const actors = ref(null);

const handlerActorName = async function() {
    actors.value = await app.request(`/admin/films/actors?name=${actorName.value}&film_id=${props.updateFilm.id}`, 'GET');
};

(async function() {
    // Получаем общий список актёров без актёров фильма
    await handlerActorName();
    // Актёры фильма
    filmActors.value = await app.request(`/admin/films/getActorsList/${props.updateFilm.id}`, 'GET');
})();

const onBeforeForHandlerAddActorInFilm = () => { app.isRequest = true; };

const onSuccessForHandlerAddActorInFilm = () => { props.hideUpdateFilmActorsModal(); };

const onFinishForHandlerAddActorInFilm = () => { app.isRequest = false; };

const handlerAddActorInFilm = function(e) {
    const actor_id = e.target.getAttribute('data-id');
    
    router.post(filmsAdmin.getUrl('/admin/films/actors'), {
        film_id: props.updateFilm.id,
        actor_id
    }, {
        preserveScroll: true,
        onBefore: onBeforeForHandlerAddActorInFilm,
        onSuccess: onSuccessForHandlerAddActorInFilm,
        onFinish: onFinishForHandlerAddActorInFilm
    });
};

const isRequest = ref(false);

watch(actorName, function() {
    if(isRequest.value) {
        return;
    }
    isRequest.value = true;
    
    setTimeout(function() {
        handlerActorName();
        isRequest.value = false;
    }, 2000);
});
</script>

<template>
    <BaseModal
        :headerTitle=headerTitle
        :hideModal="hideUpdateFilmActorsModal"
    >
        <template v-slot:body>
            <h3 class="text-orange-900">Существующие актёры</h3>
                <div class="relative overflow-x-hidden overflow-y-auto h-24">
                    <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" v-if="app.isRequest"/>
                    <template v-else>
                        <div v-if="!filmActors || (filmActors && !filmActors.actors.length)">
                            Актёры не добавлены
                        </div>
                        <ul 
                            @click="showRemoveActorFromFilmModal"
                            v-if="filmActors && filmActors.actors.length"
                        >
                            <li
                                class="text-center mx-16 mb-2 p-1 border rounded-md text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer"
                                :data-id="actor.id"
                                :data-first_name="actor.first_name"
                                :data-last_name="actor.last_name"
                                v-for="actor in filmActors.actors"
                                title="Клик удалит актёра"
                            >
                                {{ actor.first_name + ' ' + actor.last_name }}
                            </li>
                        </ul>
                    </template>
                </div>
            <h3 class="text-orange-900">Добавить нового актёра</h3>
            <div class="mb-3">
                <InputField
                    titleText="Фильтр поиска актёров фильма:"
                    type="text"
                    :isInputAutofocus="true"
                    v-model="actorName"
                />
                <div class="relative overflow-x-hidden overflow-y-auto h-24">
                    <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" v-if="app.isRequest"/>
                    <template v-else>
                        <div v-if="!actors || (actors && !actors.length)">
                            Ничего не найдено
                        </div>
                        <ul 
                            @click="handlerAddActorInFilm"
                            v-if="actors && actors.length"
                        >
                            <li
                                class="text-center mx-16 mb-2 p-1 border rounded-md text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer"
                                :data-id="actor.id"
                                v-for="actor in actors"
                                title="Клик добавит актёра"
                            >
                                {{ actor.first_name + ' ' + actor.last_name }}
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </template>
    </BaseModal>
</template>

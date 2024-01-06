<script setup>
import { inject, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import InputField from '@/components/Elements/InputField.vue';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';

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
    actors.value = await app.request(`/admin/films/actors?name=${actorName.value}`, 'GET');
};
handlerActorName();

(async function() {
    filmActors.value = await app.request(`/admin/films/getActorsList/${props.updateFilm.id}`, 'GET');
})();

const handlerAddActorInFilm = function(e) {
    // Защита от повторного запроса
    if(e.target.classList.contains('disabled')) {
        return;
    }
    
    const actor_id = e.target.getAttribute('data-id');
    
    router.post(filmsAdmin.getUrl('/admin/films/actors'), {
        film_id: props.updateFilm.id,
        actor_id
    }, {
        preserveScroll: true,
        onBefore: () => {
            app.isRequest = true;
        },
        onSuccess: () => {
            props.hideUpdateFilmActorsModal();
        },
        onFinish: () => {
            app.isRequest = false;
        }
    });
};

watch(actorName, handlerActorName);
</script>

<template>
    <BaseModal
        :headerTitle=headerTitle
        :hideModal="hideUpdateFilmActorsModal"
    >
        <template v-slot:body>
            <h3 class="text-orange-900">Существующие актёры</h3>
                <div
                    v-if="!filmActors || (filmActors && !filmActors.actors.length)"
                    class="overflow-x-hidden overflow-y-auto h-24"
                >
                    Актёры не добавлены
                </div>
                <ul 
                    class="overflow-x-hidden overflow-y-auto h-24"
                    @click="showRemoveActorFromFilmModal"
                    v-if="filmActors && filmActors.actors.length"
                >
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="actor.id"
                        :data-first_name="actor.first_name"
                        :data-last_name="actor.last_name"
                        v-for="actor in filmActors.actors"
                        title="Клик удалит актёра"
                    >
                        {{ actor.first_name + ' ' + actor.last_name }}
                    </li>
                </ul>
            <h3 class="text-orange-900">Добавить нового актёра</h3>
            <div class="mb-3">
                <InputField
                    titleText="Фильтр поиска актёров фильма:"
                    type="text"
                    :isInputAutofocus="true"
                    v-model="actorName"
                />
                <div v-if="!actors || (actors && !actors.length)">
                    Ничего не найдено
                </div>
                <ul 
                    class="overflow-x-hidden overflow-y-auto h-48"
                    @click="handlerAddActorInFilm"
                    v-if="actors && actors.length"
                >
                    <li
                        class="text-center mx-16 mb-2 p-1 border rounded-md"
                        :class="app.isRequest ? 'disabled' : 'text-neutral-700 border-orange-300 hover:text-neutral-900 hover:border-orange-500 cursor-pointer'"
                        :data-id="actor.id"
                        v-for="actor in actors"
                        title="Клик добавит актёра"
                    >
                        {{ actor.first_name + ' ' + actor.last_name }}
                    </li>
                </ul>
            </div>
        </template>
    </BaseModal>
</template>

<script setup>
import { inject, ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddActorBlock from '@/Components/Pages/Admin/Actors/AddActorBlock.vue';
import RemoveActorModal from '@/Components/Modal/Request/Actors/RemoveActorModal.vue';
import UpdateActorModal from '@/Components/Modal/Request/Actors/UpdateActorModal.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

const props = defineProps({
    actors: Object,
    errors: Object
});

const titlePage = 'Актёры';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];

const actorsList = inject('actorsList');
actorsList.page = props.actors.current_page;
    
const getActorName = function(actor) {
    return actor.first_name + ' ' + actor.last_name;
};

const updateActor = reactive({
    id: 0,
    first_name: '',
    last_name: ''
});
const isShowUpdateActorModal = ref(false);
const hideUpdateActorModal = function() {
    isShowUpdateActorModal.value = false;
};

const removeActor = reactive({
    id: 0,
    first_name: '',
    last_name: ''
});
const isShowRemoveActorModal = ref(false);
const hideRemoveActorModal = function() {
    isShowRemoveActorModal.value = false;
};

// Изменяет число актёров на странице
const changeNumberOfActorsOnPage = function(newNumber) {
    actorsList.page = 1;
    actorsList.perPage = newNumber;
    router.get(actorsList.getUrl());
};

const putActors = function(e) {
    if(e.key.toLowerCase() !== "enter") {
        return;
    }
    
    actorsList.page = 1;
    router.get(actorsList.getUrl());
};

const handlerTableChange = function(e) {
    let td = e.target.closest('td');
    
    if (td && td.classList.contains('update-actor')) {
        updateActor.id = td.getAttribute('data-actor_id');
        updateActor.first_name = td.getAttribute('data-actor_first_name');
        updateActor.last_name = td.getAttribute('data-actor_last_name');
        isShowUpdateActorModal.value = true;
    }
    
    if (td && td.classList.contains('remove-actor')) {
        removeActor.id = td.getAttribute('data-actor_id');
        removeActor.first_name = td.getAttribute('data-actor_first_name');
        removeActor.last_name = td.getAttribute('data-actor_last_name');
        isShowRemoveActorModal.value = true;
    }
};
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-between mb-4">
            <Dropdown
                buttonName="Число актёров на странице"
                :itemsNumberOnPage="actors.per_page"
                :changeNumber="changeNumberOfActorsOnPage"
            />
            <AddActorBlock />
        </div>
        
        <div class="w-1/4">
            <table class="w-full" @click="handlerTableChange">
                <caption v-if="actors.total > 0">
                    Показано {{ actors.per_page }} актёров с {{ actors.from }} по {{ actors.to }} из {{ actors.total }}
                </caption>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Актёр</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><input type="text" v-model="actorsList.name" @keyup="putActors"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(actor, index) in actors.data" class="hover:bg-green-300">
                        <td class="font-sans">{{ actors.from + index }}</td>
                        <td>{{ getActorName(actor) }}</td>
                        <td class="update-actor" :data-actor_id="actor.id" :data-actor_first_name="actor.first_name" :data-actor_last_name="actor.last_name">
                            <PencilSvg
                                title="Редактировать актёра"
                            />
                        </td>
                        <td class="remove-actor" :data-actor_id="actor.id" :data-actor_first_name="actor.first_name" :data-actor_last_name="actor.last_name">
                            <TrashSvg
                                title="Удалить актёра"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div  v-if="!actors.total">
                Ни один актёр не найден, или ещё ни один актёр не добавлен.
            </div>

            <Buttons :links="actors.links" v-if="actors.total > 0" />
        </div>
        
        <RemoveActorModal
            :removeActor="removeActor"
            :hideRemoveActorModal="hideRemoveActorModal"
            v-if="isShowRemoveActorModal"
        />
        
        <UpdateActorModal
            :updateActor="updateActor"
            :hideUpdateActorModal="hideUpdateActorModal"
            v-if="isShowUpdateActorModal"
        />
    </AdminLayout>
</template>


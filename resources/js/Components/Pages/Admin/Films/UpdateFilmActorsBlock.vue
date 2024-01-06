<script setup>
import { reactive, ref } from 'vue';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';

const props = defineProps({
    updateFilm: Object,
    isShowUpdateFilmActorsModal: Boolean,
    hideUpdateFilmActorsModal: Function
});

const removeActor = reactive({
    id: 0,
    first_name: '',
    last_name: ''
});

const isRemoveActorFromFilmModal = ref(false);
const showRemoveActorFromFilmModal = function(e) {
    props.hideUpdateFilmActorsModal();
    isRemoveActorFromFilmModal.value = true;
    removeActor.id = e.target.getAttribute('data-id');
    removeActor.first_name = e.target.getAttribute('data-first_name');
    removeActor.last_name = e.target.getAttribute('data-last_name');
};
const hideRemoveActorFromFilmModal = function() {
    isRemoveActorFromFilmModal.value = false;
};
</script>

<template>
        <UpdateFilmActorsModal
            :updateFilm="updateFilm"
            :hideUpdateFilmActorsModal="hideUpdateFilmActorsModal"
            :showRemoveActorFromFilmModal="showRemoveActorFromFilmModal"
            v-if="isShowUpdateFilmActorsModal"
        />

        <RemoveActorFromFilmModal
            :updateFilm="updateFilm"
            :removeActor="removeActor"
            :hideRemoveActorFromFilmModal="hideRemoveActorFromFilmModal"
            v-if="isRemoveActorFromFilmModal"
        />
</template>

<script setup>
import { reactive, ref } from 'vue';
import { film, removeActor } from '@/Services/Content/films';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';

</script>

<template>
        <UpdateFilmActorsModal
            v-if="film.isShowUpdateFilmActorsModal"
        />

        <RemoveActorFromFilmModal
            v-if="removeActor.isRemoveActorFromFilmModal"
        />
</template>

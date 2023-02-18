<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';

const { films } = defineProps({
    title: String,
    films: Object,
    isGuest: Boolean
});

let counter = films.from;
</script>

<template>
    <Head :title="title" />
    <AppLayout :isGuest="isGuest">
        <h1>{{ title }}</h1>
        <table class="container">
            <caption>
                <Info :films='films' />
            </caption>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Язык</th>
                    <th v-if="!isGuest"></th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text"></th>
                    <th><input type="text"></th>
                    <th></th>
                    <th v-if="!isGuest"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="film in films.data">
                    <td>{{ counter++ }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                    <td v-if="!isGuest"></td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films"/>
    </Applayout>
</template>

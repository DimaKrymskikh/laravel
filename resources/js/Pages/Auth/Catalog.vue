<script setup>
import { Head } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';

const { films } = defineProps({
    films: Object
});

const titlePage = 'Каталог';

let counter = films.from;
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout>
        <h1>{{ titlePage }}</h1>
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
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text"></th>
                    <th><input type="text"></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="film in films.data">
                    <td>{{ counter++ }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                    <th></th>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films"/>
    </AuthLayout>
</template>

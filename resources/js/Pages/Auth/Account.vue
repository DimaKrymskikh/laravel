<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

const { films } = defineProps({
    films: Object,
    user: Object
});

const titlePage = 'Аккаунт';

let counter = films.from;
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout>
        <h1>Добрый день, {{ user.login }}</h1>

        <table class="container" @click="handlerFilms">
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
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text"></th>
                    <th><input type="text"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="film in films.data" class="hover:bg-green-300">
                    <td>{{ counter++ }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                    <td>
                        <EyeSvg />
                    </td>
                    <td>
                        <TrashSvg />
                    </td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films"/>
    </AuthLayout>
</template>

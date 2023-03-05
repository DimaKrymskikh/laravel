<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';

defineProps({
    films: Object,
    errors: Object | null
});

const titlePage = 'Каталог';

const addFilm = function(tag) {
    // Защита от повторного клика
    tag.classList.contains('add-film') ? tag.classList.remove('add-film') : tag.parentNode.classList.remove('add-film');
 
    const page = tag.closest('table').getAttribute('data-page');
    const filmId = tag.classList.contains('add-film') ? tag.getAttribute('data-film_id') : tag.parentNode.getAttribute('data-film_id');

    router.visit(`account/addfilm/${filmId}`, {
        method: 'post',
        preserveScroll: true,
        data: {
            page
        }
    });
};

const handlerTableChange = function(e) {
    if (e.target.classList.contains('add-film') || e.target.parentNode.classList.contains('add-film')) {
        addFilm(e.target);
    }
};
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors">
        <h1>{{ titlePage }}</h1>
        <table class="container" :data-page="films.current_page" @click="handlerTableChange">
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
                <tr v-for="(film, index) in films.data" class="hover:bg-green-300">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                    <th>
                        <CheckCircleSvg v-if="film.isAvailable" />
                        <PlusCircleSvg :filmId="film.id" v-else />
                    </th>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films"/>
    </AuthLayout>
</template>

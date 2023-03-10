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

/**
 * Добавляет фильм в коллекцию пользователя
 * @param {Element} tag
 * @returns {undefined}
 */
const addFilm = function(tag) {
    const td = tag.closest('td');
    // Защита от повторного клика
    td.classList.remove('add-film');
    
    const page = tag.closest('table').getAttribute('data-page');
    const filmId = td.getAttribute('data-film_id');

    router.visit(`account/addfilm/${filmId}`, {
        method: 'post',
        preserveScroll: true,
        data: {
            page
        }
    });
};

/**
 * Управляет изменениями в таблице фильмов
 * @param {Event} e
 * @returns {undefined}
 */
const handlerTableChange = function(e) {
    if (e.target.closest('td') && e.target.closest('td').classList.contains('add-film')) {
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
                    <td
                        :class="film.isAvailable ? null : 'add-film'"
                        :data-film_id="film.isAvailable ? null : film.id"
                    >
                        <CheckCircleSvg v-if="film.isAvailable" />
                        <PlusCircleSvg v-else />
                    </td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films"/>
    </AuthLayout>
</template>

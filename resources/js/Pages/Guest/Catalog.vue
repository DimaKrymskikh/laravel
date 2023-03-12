<script setup>
import { inject } from 'vue';
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';

const { films } = defineProps({
    films: Object,
    errors: Object | null
});

const titlePage = 'Каталог';

const paginationCatalog = inject('paginationCatalog');
paginationCatalog.setData(films.current_page, films.per_page);

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    router.get(`catalog?page=1&number=${newNumber}`);
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-between pb-4">
            <Dropdown
                buttonName="Число фильмов на странице"
                :itemsNumberOnPage="films.per_page"
                :changeNumber="changeNumberOfFilmsOnPage"
            />
        </div>
        
        <table class="container">
            <caption>
                <Info :films='films' v-if="films.total > 0" />
            </caption>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Язык</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text"></th>
                    <th><input type="text"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(film, index) in films.data">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :films="films" v-if="films.total > 0" />
    </GuestLayout>
</template>

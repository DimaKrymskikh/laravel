<script setup>
import { ref, inject } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import Bars3 from '@/Components/Svg/Bars3.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import AccountRemoveModal from '@/Components/Modal/AccountRemoveModal.vue';
import FilmRemoveModal from '@/Components/Modal/FilmRemoveModal.vue';
import FormButton from '@/Components/Elements/FormButton.vue';

const { films } = defineProps({
    films: Object,
    user: Object,
    errors: Object | null
});

const titlePage = 'Аккаунт';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: 'ЛК'
        }];

const filmsAccount = inject('filmsAccount');
filmsAccount.page = films.current_page;

const isPersonalData = ref(false);
// Выполняется ли запрос на сервер
const isRequest = ref(false);

// Отслеживает отрисовку/удаление модального окна для удаления фильма
const isShowFilmRemoveModal = ref(false);
// id удаляемого фильма
const removeFilmId = ref('');
// Название удаляемого фильма
const removeFilmTitle = ref('');

const isShowAccountRemoveModal = ref(false);

const togglePersonalData = function() {
    isPersonalData.value = !isPersonalData.value;
};

const form = useForm({});

/**
 * Управляет изменениями в таблице фильмов
 * @param {Event} e
 * @returns {undefined}
 */
const handlerTableChange = function(e) {
    // Показывает модальное окно для удаления фильма
    // Находятся id и название удаляемого фильма
    if (e.target.closest('td') && e.target.closest('td').classList.contains('remove-film')) {
        removeFilmId.value = e.target.closest('td').getAttribute('data-film_id');
        removeFilmTitle.value = e.target.closest('td').getAttribute('data-film_title');
        isShowFilmRemoveModal.value = true;
    }
};

/**
 * Скрывает модальное окно для удаления фильма
 * @returns {undefined}
 */
const hideFilmRemoveModal = function() {
    isShowFilmRemoveModal.value = false;
};

const showAccountRemoveModal = function() {
    isShowAccountRemoveModal.value = true;
};

const hideAccountRemoveModal = function() {
    isShowAccountRemoveModal.value = false;
};

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    filmsAccount.page = 1;
    filmsAccount.perPage = newNumber;
    router.get(filmsAccount.getUrl());
};

const putFilms = function(e) {
    if(e.key.toLowerCase() !== "enter") {
        return;
    }
    
    filmsAccount.page = 1;
    router.get(filmsAccount.getUrl());
};
    
const handlerVerifyEmail = function() {
    form.post('/verify-email', {
        onBefore: () => isRequest.value = true,
        onFinish: () => isRequest.value = false
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user_id="user.id">
        <BreadCrumb :linksList="linksList" />
        <div class="relative">
            <div class="absolute right-0">
                <div class="flex justify-end">
                    <Bars3 class="cursor-pointer" @click="togglePersonalData" />
                </div>
                <div class="w-80 h-screen bg-white p-4 shadow shadow-gray-500" v-if="isPersonalData">
                    <div class="text-orange-900">
                        Эл. почта:
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>{{ user.email }}</span>
                        <CheckSvg v-if="user.email_verified_at" />
                    </div>
                    <div v-if="!user.email_verified_at">
                        <div class="text-sm text-justify text-red-700">
                            Ваша эл. почта не подтверждена.
                            В почтовом ящике должно быть письмо со ссылкой для подтверждения.
                            Вы можете нажать на эту кнопку для отправки нового письма
                        </div>
                        <div class="text-center">
                            <form @submit.prevent="handlerVerifyEmail">
                                <FormButton class="w-56" text="Отправка нового письма" :form="form" :isRequest="isRequest" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h1>Добрый день, {{ user.login }}</h1>

                <div class="flex justify-between pb-4">
                    <Dropdown
                        buttonName="Число фильмов на странице"
                        :itemsNumberOnPage="films.per_page"
                        :changeNumber="changeNumberOfFilmsOnPage"
                    />
                    <button
                        class="px-4 py-2 bg-red-100 text-red-700 hover:bg-red-300 hover:text-red-900 rounded-lg"
                        @click="showAccountRemoveModal"
                    >
                        Удалить аккаунт
                    </button>
                </div>

                <table class="container" @click="handlerTableChange">
                    <caption>
                        <Info :films='films' v-if="films.total > 0" />
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
                            <th><input type="text" v-model="filmsAccount.title" @keyup="putFilms"></th>
                            <th><input type="text" v-model="filmsAccount.description" @keyup="putFilms"></th>
                            <th></th>
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
                            <td>
                                <Link :href="`/filmcard/${film.id}`"><EyeSvg /></Link>
                            </td>
                            <td class="remove-film" :data-film_id="film.id" :data-film_title="film.title">
                                <TrashSvg />
                            </td>
                        </tr>
                    </tbody>
                </table>

                <Buttons :films="films" v-if="films.total > 0" />
            </div>
        </div>
        
        <FilmRemoveModal
            :films="films"
            :errors="errors"
            :removeFilmTitle="removeFilmTitle"
            :removeFilmId="removeFilmId"
            :hideFilmRemoveModal="hideFilmRemoveModal"
            v-if="isShowFilmRemoveModal"
        />
        
        <AccountRemoveModal
            :errors="errors"
            :hideAccountRemoveModal="hideAccountRemoveModal"
            v-if="isShowAccountRemoveModal"
        />
    </AuthLayout>
</template>

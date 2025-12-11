<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';

const filmsAccount = inject('filmsAccount');
</script>

<template>
    <ul class="mt-0.5 pt-16 bg-orange-50">
        <li class="tabs-li">
            <Link 
                class="block px-4 py-1 small-caps"
                :class="{ 'tabs-link-active': $page.component === 'Auth/Account/UserFilms' }"
                :href="filmsAccount.getUrl('/userfilms')"
            >
                фильмы
            </Link>
        </li>
        <li class="tabs-li">
            <Link 
                class="block px-4 py-1 small-caps"
                :class="{ 'tabs-link-active': $page.component === 'Auth/Account/UserWeather' }"
                href="/userweather"
            >
                погода
            </Link>
        </li>
        <li class="tabs-li">
            <Link 
                class="block px-4 py-1 small-caps"
                :class="{ 'tabs-link-active': $page.component === 'Auth/Account/TrialResults' }"
                href="/trials/get_results"
            >
                результаты опросов
            </Link>
        </li>
    </ul>
</template>

<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';

const filmsAccount = inject('filmsAccount');
</script>

<template>
    <ul>
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
    </ul>
</template>

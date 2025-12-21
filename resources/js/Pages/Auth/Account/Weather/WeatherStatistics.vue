<script setup>
import { inject } from 'vue';
import { Head } from '@inertiajs/vue3'
import { messageEmptyTable, statisticsOptions } from '@/Services/Content/Weather/weatherStatistics';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import LinkBlock from '@/Components/Pages/Auth/Account/Weather/weatherStatistics/LinkBlock.vue';
import WeatherOptionsBlock from '@/Components/Pages/Auth/Account/Weather/weatherStatistics/WeatherOptionsBlock.vue';

const props = defineProps({
    weatherPage: Object,
    city: Object,
    user: Object,
    errors: Object | null
});

const titlePage = 'Статистика погоды в городе ' + props.city.name;

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            link: '/userweather',
            text: 'ЛК: погода'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">
        <h2 class="mb-6 text-orange-700">{{ titlePage }}</h2>

        <WeatherOptionsBlock />
        <div class="mb-4">
            <LinkBlock :city="city"/>
        </div>
        
        <table class="container">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Начало периода</th>
                    <th>Конец периода</th>
                    <th>Средняя температура</th>
                    <th>Максимальная температура</th>
                    <th>Минимальная температура</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(weather, index) in weatherPage" class="hover:bg-green-300">
                    <td class="text-center">
                        <span class="font-sans">{{ index + 1 }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.datefrom }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.dateto }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.avg }}</span>
                    </td>
                    <td>
                        <span class="font-sans">{{ weather.max ? `${weather.max} (${weather.max_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                    <td>
                        <span class="font-sans">{{ weather.min ? `${weather.min} (${weather.min_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
            
        <div  v-if="!weatherPage.length" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
    </AccountLayout>
</template>

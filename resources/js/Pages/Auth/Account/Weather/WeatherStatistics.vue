<script setup>
import { inject } from 'vue';
import { Head } from '@inertiajs/vue3'
import { messageEmptyTable, statisticsOptions } from '@/Services/Content/Weather/weatherStatistics';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import LinkBlock from '@/Components/Pages/Auth/Account/Weather/weatherStatistics/LinkBlock.vue';
import WeatherOptionsBlock from '@/Components/Pages/Auth/Account/Weather/weatherStatistics/WeatherOptionsBlock.vue';

const props = defineProps({
    weather: Object,
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
                <tr v-if="weather.weatherAll">
                    <td colspan="6" class="text-center">
                        <span class="text-orange-700">За весь период</span>
                    </td>
                </tr>
                <tr v-if="weather.weatherAll" class="hover:bg-green-300">
                    <td class="text-center">
                        <span class="font-sans">0</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.weatherAll.datefrom }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.weatherAll.dateto }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.weatherAll.avg ? weather.weatherAll.avg : 'нет измерений за этот период' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.weatherAll.max ? `${weather.weatherAll.max} (${weather.weatherAll.max_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.weatherAll.min ? `${weather.weatherAll.min} (${weather.weatherAll.min_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                </tr>
                <tr v-if="weather.weatherIntervals">
                    <td colspan="6" class="text-center">
                        <span class="text-orange-700">Разбивка на интервалы</span>
                    </td>
                </tr>
                <tr v-if="weather.weatherIntervals" v-for="(weather, index) in weather.weatherIntervals" class="hover:bg-green-300">
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
                        <span class="font-sans">{{ weather.avg ? weather.avg : 'нет измерений за этот период' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.max ? `${weather.max} (${weather.max_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="font-sans">{{ weather.min ? `${weather.min} (${weather.min_time})` : 'нет измерений за этот период' }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
            
        <div  v-if="!weather.weatherAll && !weather.weatherIntervals" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
    </AccountLayout>
</template>

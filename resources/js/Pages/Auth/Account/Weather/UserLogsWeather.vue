<script setup>
import { Head, router } from '@inertiajs/vue3'
import { paginationOptionsForWeatherLogs } from "@/Services/Content/Weather/weatherLogs.js";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import RemoveCityFromListOfWeatherModal from '@/Components/Pages/Auth/Account/UserWeather/RemoveCityFromListOfWeatherModal.vue';
import WeatherFilterByDateBlock from '@/Components/Pages/Auth/Account/WeatherFilterByDateBlock.vue';

const props = defineProps({
    weatherPage: Object,
    city: Object,
    user: Object,
    errors: Object | null
});

const titlePage = 'История погоды в городе ' + props.city.name;

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

// Отправляет запрос на получение нового списка 
function refreshWeather() {
    router.get(paginationOptionsForWeatherLogs.getUrl(`/userlogsweather/${props.city.id}`));
}

// Изменяет число записей погоды на странице
const changeNumberOfWeatherOnPage = function(newNumber) {
    paginationOptionsForWeatherLogs.page = 1;
    paginationOptionsForWeatherLogs.perPage = newNumber;
    refreshWeather();
};
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">
        <div>
            <h2 class="mb-6 text-orange-700">{{ titlePage }}</h2>

            <div class="flex justify-start pb-4">
                <Dropdown
                    buttonName="Число записей погоды на странице"
                    :itemsNumberOnPage="weatherPage.per_page"
                    :changeNumber="changeNumberOfWeatherOnPage"
                />
            </div>

            <WeatherFilterByDateBlock :refreshWeather="refreshWeather" />

            <table class="container">
                <caption>
                    Показано {{ weatherPage.per_page }} показаний погоды с {{ weatherPage.from }} по {{ weatherPage.to }} из {{ weatherPage.total }}
                </caption>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Время и дата</th>
                        <th>Описание</th>
                        <th>Температура</th>
                        <th>Атм. давление</th>
                        <th>Влажность</th>
                        <th>Видимость</th>
                        <th>Ветер</th>
                        <th>Облачность</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(weather, index) in weatherPage.data" class="hover:bg-green-300">
                        <td>
                            <span class="font-sans">{{ weatherPage.from + index }}</span>
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.created_at }}</span>
                        </td>
                        <td class="text-center">
                            {{ weather.weather_description }}
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.main_temp }}</span> C&deg;
                            ( ощущается как 
                            <span class="font-sans">{{ weather.main_feels_like }}</span> C&deg; )
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.main_pressure }}</span> hPa
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.main_humidity }}</span>%
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.visibility }}</span> метров
                        </td>
                        <td class="text-center">
                            <div>
                                скорость ветра
                                <span class="font-sans">{{ weather.wind_speed }}</span> м/c
                            </div>
                            <div>
                                направление ветра
                                <span class="font-sans">{{ weather.wind_deg }}</span>&deg;
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="font-sans">{{ weather.clouds_all }}</span>%
                        </td>
                    </tr>
                </tbody>
            </table>

            <Buttons :links="weatherPage.links" v-if="weatherPage.total > 0" />
        </div>
    </AccountLayout>
</template>

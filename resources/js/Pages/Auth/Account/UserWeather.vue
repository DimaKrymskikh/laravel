<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';

const {cities} = defineProps({
    cities: Array,
    user: Object,
    errors: Object | null
});

const titlePage = 'ЛК: погода';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">
        <div class="mx-4 w-1/2 mb-4">
            <div class="flex justify-between border-b">
                <div class="w-1/4 pr-2">
                    <h3 class="text-orange-700">Город</h3>
                </div>
                <div class="w-3/4 pl-2">
                    <h3 class="text-orange-700">Последние данные о погоде</h3>
                </div>
            </div>
            <template v-for="(city, index) in cities">
                <div class="flex justify-between border-b">
                    <div class="w-1/4 pr-2">
                        <span class="font-sans mr-2">{{ index + 1 }}</span> 
                        <span>{{ city.name }}</span>
                    </div>
                    <div class="w-3/4 pl-2">
                        <template v-if="city.weather_first">
                            <div>
                                <span class="font-semibold mr-2">Время и дата:</span> 
                                <span class="font-sans">{{ city.weather_first.created_at }}</span>
                                <span v-if="!city.timezone_id" class="text-red-700"> (часовой пояс не указан) </span>
                            </div>
                            <div>{{ city.weather_first.weather_description }}</div>
                            <div>
                                <span class="font-semibold mr-2">Температура:</span>
                                <span class="font-sans">{{ city.weather_first.main_temp }}</span> C&deg;,
                                ощущается как 
                                <span class="font-sans">{{ city.weather_first.main_feels_like }}</span> C&deg;
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Атмосферное давление:</span>
                                <span class="font-sans">{{ city.weather_first.main_pressure }}</span> hPa
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Влажность:</span>
                                <span class="font-sans">{{ city.weather_first.main_humidity }}</span> %
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Видимость:</span>
                                <span class="font-sans">{{ city.weather_first.visibility }}</span> метров
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Ветер:</span>
                                скорость ветра
                                <span class="font-sans">{{ city.weather_first.wind_speed }}</span> м/c,
                                направление ветра
                                <span class="font-sans">{{ city.weather_first.wind_deg }}</span>&deg;
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Облачность:</span>
                                <span class="font-sans">{{ city.weather_first.clouds_all }}</span> %
                            </div>
                        </template>
                        <div  v-if="!city.weather_first">
                            <span class="text-red-700">Для города ещё не получены данные о погоде</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AccountLayout>
</template>

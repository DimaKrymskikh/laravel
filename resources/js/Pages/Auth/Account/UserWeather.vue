<script setup>
import { ref, reactive, inject, onUpdated } from 'vue';
import { Head, Link } from '@inertiajs/vue3'
import { app } from '@/Services/app';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import RemoveCityFromListOfWeatherModal from '@/Components/Pages/Auth/Account/UserWeather/RemoveCityFromListOfWeatherModal.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import Spinner from '@/Components/Svg/Spinner.vue';
import ArrowPathSvg from '@/Components/Svg/ArrowPathSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';

const props = defineProps({
    cities: Array,
    user: Object,
    errors: Object | null
});

const weatherPageAuth = inject('weatherPageAuth');
// Сбрасываем активную страницу пагинации для страницы погоды, 
// чтобы при смене города попасть на существующую страницу
weatherPageAuth.page = 1;

const titlePage = 'ЛК: погода';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];

// События Broadcast передаются в массиве
const pusherEvents = ['RemoveCityFromWeatherList', 'RefreshCityWeather'];
    
let cities = reactive(props.cities);

const isShowRemoveCityFromListOfWeatherModal = ref(false);

const removeCity = reactive({
    id: 0,
    name: ''
});

const refreshCityId = ref(0);
onUpdated(() => {
    cities = props.cities;
});

const hideRemoveCityFromListOfWeatherModal = function() {
    isShowRemoveCityFromListOfWeatherModal.value = false;
};
    
const handlerDataChange = function(e) {
    // Открывается модальное окно для удаления города из списка просмотра погоды
    if (e.target.closest('div') && e.target.closest('div').classList.contains('remove-city')) {
        removeCity.id = e.target.closest('div').getAttribute('data-city_id');
        removeCity.name = e.target.closest('div').getAttribute('data-city_name');
        isShowRemoveCityFromListOfWeatherModal.value = true;
    }
    // Выполняется запрос на сервер для обновления данных о погоде в городе с refreshCityId
    if (e.target.closest('div') && e.target.closest('div').classList.contains('refresh-city')) {
        refreshCityId.value = e.target.closest('div').getAttribute('data-city_id');
        app.request(`/userweather/refresh/${refreshCityId.value}`, 'POST');
    }
};

// Обновляет данные о погоде в городе
const refreshCityWeather = function(weather) {
    cities.forEach((city) => {
        if(city.id === weather.city_id) {
            city.weather = weather;
        }
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">
        <div class="mx-4 w-1/2 mb-4"  @click="handlerDataChange">
            <div class="flex justify-between border-b">
                <div class="w-3/12 pr-2">
                    <h3 class="text-orange-700">Город</h3>
                </div>
                <div class="w-8/12 pl-2">
                    <h3 class="text-orange-700">Последние данные о погоде</h3>
                </div>
                <div class="w-1/12"></div>
            </div>
            <template v-for="(city, index) in cities">
                <div class="flex justify-between border-b">
                    <div class="w-3/12 pr-2">
                        <span class="font-sans mr-2">{{ index + 1 }}</span> 
                        <span>{{ city.name }}</span>
                        <Link :href="weatherPageAuth.getUrl(`/userlogsweather/${city.id}`)">
                            <EyeSvg title="Посмотреть историю погоды в городе"/>
                        </Link>
                    </div>
                    <div class="w-8/12 pl-2">
                        <template v-if="city.weather">
                            <div>
                                <span class="font-semibold mr-2">Время и дата:</span> 
                                <span class="font-sans">{{ city.weather.created_at }}</span>
                                <span v-if="!city.timezone_id" class="text-red-700"> (часовой пояс не указан) </span>
                            </div>
                            <div>{{ city.weather.weather_description }}</div>
                            <div>
                                <span class="font-semibold mr-2">Температура:</span>
                                <span class="font-sans">{{ city.weather.main_temp }}</span> C&deg;,
                                ощущается как 
                                <span class="font-sans">{{ city.weather.main_feels_like }}</span> C&deg;
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Атмосферное давление:</span>
                                <span class="font-sans">{{ city.weather.main_pressure }}</span> hPa
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Влажность:</span>
                                <span class="font-sans">{{ city.weather.main_humidity }}</span> %
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Видимость:</span>
                                <span class="font-sans">{{ city.weather.visibility }}</span> метров
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Ветер:</span>
                                скорость ветра
                                <span class="font-sans">{{ city.weather.wind_speed }}</span> м/c,
                                направление ветра
                                <span class="font-sans">{{ city.weather.wind_deg }}</span>&deg;
                            </div>
                            <div>
                                <span class="font-semibold mr-2">Облачность:</span>
                                <span class="font-sans">{{ city.weather.clouds_all }}</span> %
                            </div>
                        </template>
                        <div  v-if="!city.weather">
                            <span class="text-red-700">Для города ещё не получены данные о погоде</span>
                        </div>
                    </div>
                    <div class="w-1/12 flex flex-col justify-start">
                        <div class="pt-4 flex justify-center refresh-city" :data-city_id="city.id">
                            <Spinner styleSpinner="h-4 text-orange-200 fill-orange-700" class="flex justify-center" v-if="app.isRequest && refreshCityId == city.id" />
                            <ArrowPathSvg title="Получить последние данные погоды в городе" v-else/>
                        </div>
                        <div class="pt-4 flex justify-center remove-city" :data-city_id="city.id"  :data-city_name="city.name">
                            <TrashSvg title="Удалить город из списка просмотра погоды"/>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <RemoveCityFromListOfWeatherModal
            :removeCity="removeCity"
            :hideRemoveCityModal="hideRemoveCityFromListOfWeatherModal"
            v-if="isShowRemoveCityFromListOfWeatherModal"
        />
        
        <EchoAuth :user="user" :action="refreshCityWeather" :events="pusherEvents" />
    </AccountLayout>
</template>

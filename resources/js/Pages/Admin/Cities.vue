<script setup>
import { ref, reactive } from 'vue';
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddCityBlock from '@/Components/Pages/Admin/Cities/AddCityBlock.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import RemoveCityModal from '@/Components/Modal/Request/Cities/RemoveCityModal.vue';
import UpdateCityModal from '@/Components/Modal/Request/Cities/UpdateCityModal.vue';
import UpdateTimeZoneModal from '@/Components/Modal/Request/UpdateTimeZoneModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

defineProps({
    cities: Array | null,
    errors: Object
});

const titlePage = 'Города';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];

// При загрузке страницы модальное окно для удаления фильма отсутствует.
const isShowRemoveCityModal = ref(false);
// Удаляемый город.
// Задаётся в handlerTableChange.
const removeCity = reactive({
    id: 0,
    name: '',
    open_weather_id: 0
});
const hideRemoveCityModal = function() {
    isShowRemoveCityModal.value = false;
};

const isShowUpdateCityModal = ref(false);
const updateCity = reactive({
    id: 0,
    name: ''
});
const hideUpdateCityModal = function() {
    isShowUpdateCityModal.value = false;
};

const isShowUpdateTimeZoneModal = ref(false);
const hideUpdateTimeZoneModal = function() {
    isShowUpdateTimeZoneModal.value = false;
};

const handlerTableChange = function(e) {
    let td = e.target.closest('td');
    
    // Показывает модальное окно для удаления города
    // Находятся поля удаляемого города
    if (td && td.classList.contains('remove-city')) {
        removeCity.id = td.getAttribute('data-city_id');
        removeCity.name = td.getAttribute('data-city_name');
        removeCity.open_weather_id = td.getAttribute('data-city_open_weather_id');
        isShowRemoveCityModal.value = true;
    }
    
    // Показывает модальное окно для изменения имени города
    if (td && td.classList.contains('update-city')) {
        updateCity.id = td.getAttribute('data-city_id');
        updateCity.name = td.getAttribute('data-city_name');
        isShowUpdateCityModal.value = true;
    }
    
    // Показывает модальное окно для изменения временного пояса города
    if (td && td.classList.contains('update-timezone')) {
        updateCity.id = td.getAttribute('data-city_id');
        updateCity.name = td.getAttribute('data-city_name');
        isShowUpdateTimeZoneModal.value = true;
    }
};

</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-start mb-4">
            <AddCityBlock />
        </div>
        
        <table @click="handlerTableChange" v-if="cities.length">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Город</th>
                    <th></th>
                    <th>Временной пояс</th>
                    <th></th>
                    <th>OpenWeather Id</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(city, index) in cities" class="hover:bg-green-300">
                    <td class="font-sans">{{ index + 1 }}</td>
                    <td>{{ city.name }}</td>
                    <td class="update-city" :data-city_id="city.id" :data-city_name="city.name">
                        <PencilSvg
                            title="Редактировать название фильма"
                        />
                    </td>
                    <td>{{ city.timezone ? city.timezone.name : 'не задан' }}</td>
                    <td class="update-timezone" :data-city_id="city.id" :data-city_name="city.name">
                        <PencilSvg
                            title="Редактировать временной пояс"
                        />
                    </td>
                    <td class="font-sans">{{ city.open_weather_id }}</td>
                    <td class="remove-city" :data-city_id="city.id" :data-city_name="city.name" :data-city_open_weather_id="city.open_weather_id">
                        <TrashSvg
                            title="Удалить город"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else>
            Ещё ни один город не добавлен
        </div>
        
        <RemoveCityModal
            :removeCity="removeCity"
            :hideRemoveCityModal="hideRemoveCityModal"
            v-if="isShowRemoveCityModal"
        />
        
        <UpdateCityModal
            :updateCity="updateCity"
            :hideUpdateCityModal="hideUpdateCityModal"
            v-if="isShowUpdateCityModal"
        />
        
        <UpdateTimeZoneModal
            :updateCity="updateCity"
            :hideUpdateTimeZoneModal="hideUpdateTimeZoneModal"
            v-if="isShowUpdateTimeZoneModal"
        />
    </AdminLayout>
</template>

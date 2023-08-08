<script setup>
import { ref, reactive } from 'vue';
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddCityModal from '@/Components/Modal/Request/AddCityModal.vue';
import RemoveCityModal from '@/Components/Modal/Request/RemoveCityModal.vue';
import UpdateCityModal from '@/Components/Modal/Request/UpdateCityModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

defineProps({
    cities: Array | null,
    errors: Object | null
});

const titlePage = 'Города';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];

const isShowAddCityModal = ref(false);

const showAddCityModal = function() {
    isShowAddCityModal.value = true;
};
const hideAddCityModal = function() {
    isShowAddCityModal.value = false;
};

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
    
    if (td && td.classList.contains('update-city')) {
        updateCity.id = td.getAttribute('data-city_id');
        updateCity.name = td.getAttribute('data-city_name');
        isShowUpdateCityModal.value = true;
    }
};

</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-start mb-4">
            <PrimaryButton
                id="add-city"
                buttonText="Добавить город"
                :handler="showAddCityModal"
            />
        </div>
        
        <table @click="handlerTableChange" v-if="cities.length">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Город</th>
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
        
        <AddCityModal
            :hideAddCityModal="hideAddCityModal"
            v-if="isShowAddCityModal"
        />
        
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
    </AdminLayout>
</template>

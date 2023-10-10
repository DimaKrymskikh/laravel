<script setup>
import { ref, inject } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const { films } = defineProps({
    cities: Object,
    user: Object,
    errors: Object | null
});

const app = inject('app');

const titlePage = 'Города';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];

// id фильма, который добавляется в коллекцию пользователя
const cityId = ref(null);

const addCity = function(tag) {
    const td = tag.closest('td');
    // Защита от повторного клика
    td.classList.remove('add-city');
    
    cityId.value = td.getAttribute('data-city_id');

    router.post(`/cities/addcity/${cityId.value}`, {}, {
            preserveScroll: true,
            onBefore: () => app.isRequest = true,
            onFinish: () => app.isRequest = false
        });
};

const handlerTableChange = function(e) {
    if (e.target.closest('td') && e.target.closest('td').classList.contains('add-city')) {
        addCity(e.target);
    }
};
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        
        <div class="flex w-1/2">
            <div class="w-1/2">
                <table @click="handlerTableChange" v-if="cities.length">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Город</th>
                            <th>Временной пояс</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(city, index) in cities" class="hover:bg-green-300">
                            <td class="font-sans">{{ index + 1 }}</td>
                            <td>{{ city.name }}</td>
                            <td>{{ city.timezone ? city.timezone.name : 'не задан' }}</td>
                            <td
                                :class="city.isAvailable ? null : 'add-city'"
                                :data-city_id="city.isAvailable ? null : city.id"
                            >
                                <Spinner styleSpinner="h-4 text-orange-200 fill-orange-700" class="flex justify-center" v-if="app.isRequest && cityId == city.id" />
                                <template v-else>
                                    <CheckCircleSvg v-if="city.isAvailable" title="Данный город уже в вашей коллекции" />
                                    <PlusCircleSvg v-else title="Добавить город в коллекцию" />
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    Ещё ни один город не добавлен
                </div>
            </div>

            <div class="w-1/2">
                Выберите города, в которых хотите просмотреть погоду
            </div>
        </div>
        
    </AuthLayout>
</template>

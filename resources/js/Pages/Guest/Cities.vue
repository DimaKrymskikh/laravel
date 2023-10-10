<script setup>
import { ref, inject } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

const { films } = defineProps({
    cities: Object,
    errors: Object | null
});

const titlePage = 'Города';

// Список для хлебных крошек
const linksList = [{
            link: '/guest',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        
        <div class="flex w-1/2">
            <div class="w-1/2">
                <table v-if="cities.length">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Город</th>
                            <th>Временной пояс</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(city, index) in cities" class="hover:bg-green-300">
                            <td class="font-sans">{{ index + 1 }}</td>
                            <td>{{ city.name }}</td>
                            <td>{{ city.timezone ? city.timezone.name : 'не задан' }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    Ещё ни один город не добавлен
                </div>
            </div>

            <div class="w-1/2">
                Города, в которых доступен прогноз погоды.
                Авторизуйтесь, чтобы узнать погоду в выбранном городе.
            </div>
        </div>
        
    </GuestLayout>
</template>

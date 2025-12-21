<script setup>
import { Link } from '@inertiajs/vue3'
import { statisticsOptions } from '@/Services/Content/Weather/weatherStatistics';

const props = defineProps({
    city: Object
});
</script>

<template>
    <Link
        v-if="statisticsOptions.datefrom && statisticsOptions.dateto && statisticsOptions.interval"
        class="text-orange-900"
        :href="statisticsOptions.getUrl(city.id)">
        Получить статистику для города {{ city.name }}
    </Link>
    <div v-else class="text-red-700 flex">
        <div v-if="!statisticsOptions.datefrom" class="mr-4">Не задано начало промежутка.</div>
        <div v-if="!statisticsOptions.dateto" class="mr-4">Не задан конец промежутка.</div>
        <div v-if="!statisticsOptions.interval">Не задан интервал.</div>
    </div>
</template>

<script setup>
import { statisticsOptions } from '@/Services/Content/Weather/weatherStatistics';
import InputPikaday from '@/Components/Elements/InputPikaday.vue';
import BackSpaceSvg from '@/Components/Svg/BackSpaceSvg.vue';
import PeriodicityIntervalDropdown from '@/Components/Pages/Auth/Account/Weather/WeatherStatistics/PeriodicityIntervalDropdown.vue';

statisticsOptions.urlParams();
</script>

<template>
    <div class="flex justify-between items-start mb-8">
        <div class="flex justify-start">
            Показать статистику погоду
            с <InputPikaday class="ml-2" datepicker="from" v-model="statisticsOptions.datefrom"/>
            <BackSpaceSvg class="mr-2" title="Очистить дату" @click="statisticsOptions.clearDatefrom()"/>
            по <InputPikaday class="ml-2" datepicker="to" v-model="statisticsOptions.dateto"/>
            <BackSpaceSvg title="Очистить дату" @click="statisticsOptions.clearDateto()"/>
        </div>
        <PeriodicityIntervalDropdown />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { paginationOptionsForWeatherLogs } from "@/Services/Content/Weather/weatherLogs.js";
import InputPikaday from '@/Components/Elements/InputPikaday.vue';
import BackSpaceSvg from '@/Components/Svg/BackSpaceSvg.vue';

const props = defineProps({
    refreshWeather: Function
});

paginationOptionsForWeatherLogs.urlParams();

const datefrom = ref(paginationOptionsForWeatherLogs.datefrom);
const dateto = ref(paginationOptionsForWeatherLogs.dateto);

const clearDatefrom = function() {
    datefrom.value = '';
};

const clearDateto = function() {
    dateto.value = '';
};

watch(datefrom, () => {
    paginationOptionsForWeatherLogs.datefrom = datefrom.value;
    paginationOptionsForWeatherLogs.page = 1;
    props.refreshWeather();
});

watch(dateto, () => {
    paginationOptionsForWeatherLogs.dateto = dateto.value;
    paginationOptionsForWeatherLogs.page = 1;
    props.refreshWeather();
});
</script>

<template>
    <div class="flex justify-start pb-4">
        Показать погоду
        с <InputPikaday class="ml-2" datepicker="from" v-model="datefrom"/>
        <BackSpaceSvg class="mr-2" title="Очистить дату" @click="clearDatefrom"/>
        по <InputPikaday class="ml-2" datepicker="to" v-model="dateto"/>
        <BackSpaceSvg title="Очистить дату" @click="clearDateto"/>
    </div>
</template>

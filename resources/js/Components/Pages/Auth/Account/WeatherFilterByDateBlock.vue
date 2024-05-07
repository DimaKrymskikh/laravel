<script setup>
import { ref, inject, watch } from 'vue';
import ImputPikaday from '@/Components/Elements/ImputPikaday.vue';
import BackSpaceSvg from '@/Components/Svg/BackSpaceSvg.vue';

const props = defineProps({
    refreshWeather: Function
});

const weatherPageAuth = inject('weatherPageAuth');

// Заполняем поля дат по данным запроса (актуально при обновлении страницы Ctrl-F5)
const urlParams = new URLSearchParams(window.location.search);
// Если параметр отсутствует, сохраняем пустую строку
weatherPageAuth.datefrom = !!urlParams.get('datefrom') ? urlParams.get('datefrom') : '';
weatherPageAuth.dateto = !!urlParams.get('dateto') ? urlParams.get('dateto') : '';

const datefrom = ref(weatherPageAuth.datefrom);
const dateto = ref(weatherPageAuth.dateto);

const clearDatefrom = function() {
    datefrom.value = '';
};

const clearDateto = function() {
    dateto.value = '';
};

watch(datefrom, () => {
    weatherPageAuth.datefrom = datefrom.value;
    weatherPageAuth.page = 1;
    props.refreshWeather();
});

watch(dateto, () => {
    weatherPageAuth.dateto = dateto.value;
    weatherPageAuth.page = 1;
    props.refreshWeather();
});
</script>

<template>
    <div class="flex justify-start pb-4">
        Показать погоду
        с <ImputPikaday class="ml-2" datepicker="from" v-model="datefrom"/>
        <BackSpaceSvg class="mr-2" title="Очистить дату" @click="clearDatefrom"/>
        по <ImputPikaday class="ml-2" datepicker="to" v-model="dateto"/>
        <BackSpaceSvg title="Очистить дату" @click="clearDateto"/>
    </div>
</template>

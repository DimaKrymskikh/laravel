/**
 * Модуль для страницы со статистикой погоды
 */

import { reactive } from 'vue';

export const messageEmptyTable = 'Данные не найдены. Нужно изменить настройки периода наблюдений.';

export const periodicityIntervals = ['day', 'week', 'month', 'year'];

export const statisticsOptions = reactive({
    datefrom: '',
    dateto: '',
    interval: '',
    clearDatefrom() {
        this.datefrom = '';
    },
    clearDateto() {
        this.dateto = '';
    },
    getIntervalText(interval) {
        switch(interval) {
            case 'day': return 'день';
            case 'week': return 'неделя';
            case 'month': return 'месяц';
            case 'year': return 'год';
        }
    },
    getUrl(id) {
        return `/userlogsweather/get_statistics/${id}?datefrom=${this.datefrom}&dateto=${this.dateto}&interval=${this.interval}`;
    }
});

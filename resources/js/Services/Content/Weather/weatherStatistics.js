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
    },
    urlParams() {
        // Получаем параметры запроса (актуально при обновлении страницы Ctrl-F5)
        const urlParams = new URLSearchParams(window.location.search);
        // Если параметр отсутствует, сохраняем пустую строку
        this.datefrom = !!urlParams.get('datefrom') ? urlParams.get('datefrom') : '';
        this.dateto = !!urlParams.get('dateto') ? urlParams.get('dateto') : '';
        this.interval = !!urlParams.get('interval') ? urlParams.get('interval') : '';
    }
});

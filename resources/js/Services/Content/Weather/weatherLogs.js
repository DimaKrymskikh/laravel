/**
 * Модуль для страницы с историей погоды
 */

import { reactive } from 'vue';
import { paginationOptions } from "@/Services/pagination.js";

export const paginationOptionsForWeatherLogs = reactive({
    ...paginationOptions,
    datefrom: '',
    dateto: '',
    getUrl(url) {
        return `${url}?page=${this.page}&number=${this.perPage}&datefrom=${this.datefrom}&dateto=${this.dateto}`;
    },
    urlParams() {
        // Получаем параметры запроса (актуально при обновлении страницы Ctrl-F5)
        const urlParams = new URLSearchParams(window.location.search);
        // Если параметр отсутствует, сохраняем пустую строку
        this.datefrom = !!urlParams.get('datefrom') ? urlParams.get('datefrom') : '';
        this.dateto = !!urlParams.get('dateto') ? urlParams.get('dateto') : '';
    }
});

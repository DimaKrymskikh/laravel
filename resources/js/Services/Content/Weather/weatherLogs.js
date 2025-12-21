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
    }
});

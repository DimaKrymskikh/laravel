/**
 * Модуль для опросов пользователя
 */

import '@/bootstrap';

import { reactive } from 'vue';
import { modal } from '@/Services/app';

export const messageEmptyTable = 'Нет опросов для прохождения.';

export const messageEmptyTableForResults = 'Ещё ни один опрос не был пройден.';

/**
 * Объект для модального окна, в котором пользователь даёт ответ на вопрос
 */
export const trialQuestions = reactive({
    ...modal,
    show(answer) {
        this.isShow = true;
        this.activeQuestion = answer;
    },
    activeQuestion: undefined
});

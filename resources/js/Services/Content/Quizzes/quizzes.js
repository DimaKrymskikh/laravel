/**
 * Модуль для работы с опросами
 */

import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { app, modal } from '@/Services/app';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';

// Должно совпадать с App\Models\Quiz::DEFAULT_LAED_TIME
const defaultLeadTime = '20';

export const messageEmptyTable = 'Ни один опрос не найден, или ещё ни один опрос не добавлен.';

/**
 * Объект модального окна, в котором создаётся новый опрос
 */
export const newQuiz = reactive({
    ...modal,
    show() {
        this.isShow = true;
        this.title = '';
        this.description = '';
        this.leadTime = defaultLeadTime;
    },
    title: '',
    description: '',
    leadTime: '0'
});

/**
 * Объект модального окна, в котором опросу задаётся статус 'удалён'
 */
export const removedQuiz = reactive({
    ...modal,
    isRemoved: false,
    id: 0,
    title: ''
});

/**
 * Объект модального окна, в котором опросу задаётся статус 'утверждён'
 */
export const approvedQuiz = reactive({
    ...modal,
    isApproved: false,
    id: 0,
    title: ''
});

/**
 * Объект, содержащий свойства и методы, которые необходимы для изменения поля в таблице 'quiz.quizzes'
 */
export const activeField = reactive({
    id: undefined,
    field: undefined,
    url: undefined,
    modal: null,
    errorsMessage: '',
    
    set(id, field, url, modal) {
        activeField.id = id;
        activeField.field = field;
        activeField.url = url;
        activeField.modal = modal;
    },
    
    reset() {
        activeField.id = undefined;
        activeField.field = undefined;
        activeField.url = undefined;
        activeField.modal = null;
    },
    
    onBefore() {
        defaultOnBefore();
        activeField.errorsMessage = '';
    },
    
    onSuccess() {
        activeField.modal.hide();
    },
    
    onError(errors) {
        activeField.errorsMessage = errors[activeField.field] ? errors[activeField.field] : '';
        defaultOnError(activeField.modal.hide.bind(activeField.modal))(errors);
    },
    
    update(value) {
        router.put(activeField.url, {
            field: activeField.field,
            value
        }, {
            preserveScroll: true,
            onBefore: activeField.onBefore,
            onSuccess: activeField.onSuccess,
            onError: activeField.onError,
            onFinish: defaultOnFinish
        });
    }
});

/**
 * Объект модального окна, в котором редактируется поле опроса
 */
export const fieldModal = {
    isShow: false,
    show(id, field, url) {
        if (app.isRequest) {
            return;
        }
        this.isShow = true;
        activeField.errorsMessage = '';
        activeField.set(id, field, url, this);
    },
    hide() {
        this.isShow = false;
        activeField.reset();
    },
    
    hideWithoutRequest() {
        if (!app.isRequest) {
            this.hide();
        }
    }
};

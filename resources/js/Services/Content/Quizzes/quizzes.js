import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import { modal } from '@/Services/app';

// Должно совпадать с App\Models\Quiz::DEFAULT_LAED_TIME
const defaultLeadTime = '20';

export const messageEmptyTable = 'Ни один опрос не найден, или ещё ни один опрос не добавлен.';

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

export const updateQuiz = {
    ...modal,
    
    errorsMessage: '',
    
    hideOnCross() {
        if (!app.isRequest) {
            this.hide();
        }
    },
    
    onBefore() {
        defaultOnBefore();
        this.errorsMessage = '';
    },
    onSuccess() {
        this.hide();
    },
    onError(errors) {
        this.errorsMessage = errors[this.field] ? errors[this.field] : '';
        defaultOnError(this.hide.bind(this))(errors);
    }
};

export const updateQuizField = function(id, fieldValue, updateQuiz) {
    return function() {
        router.put(`/admin/quizzes/${id}`, {
            field: updateQuiz.field,
            value: fieldValue.value
        }, {
            preserveScroll: true,
            onBefore: updateQuiz.onBefore.bind(updateQuiz),
            onSuccess: updateQuiz.onSuccess.bind(updateQuiz),
            onError: updateQuiz.onError.bind(updateQuiz),
            onFinish: defaultOnFinish
        });
    };
};

export const removedQuiz = reactive({
    ...modal,
    isRemoved: false,
    id: 0,
    title: ''
});

export const approvedQuiz = reactive({
    ...modal,
    isApproved: false,
    id: 0,
    title: ''
});

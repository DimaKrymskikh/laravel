import { reactive } from 'vue';
import { modal } from '@/Services/app';

export const messageEmptyTable = 'У данного опроса ещё нет вопросов.';

export const currentQuiz = reactive({
    isEditable: true,
    setIsEditable(quiz) {
        this.isEditable = quiz.status.isEditable;
    }
});

export const newQuizItem = reactive({
    ...modal,
    show() {
        this.isShow = true;
        this.description = '';
    },
    description: ''
});

export const removedQuizItem = reactive({
    ...modal,
    isRemoved: false,
    id: 0,
    description: '',
    quizTitle: ''
});

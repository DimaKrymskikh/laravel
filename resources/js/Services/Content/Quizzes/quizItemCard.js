import { reactive } from 'vue';
import { modal } from '@/Services/app';

export const messageEmptyTable = 'Для этого вопроса ещё нет ни одного ответа.';

export const currentQuizItem = reactive({
    isEditable: true,
    setIsEditable(quizItem) {
        this.isEditable = quizItem.status_info.isEditable && quizItem.quiz.status_info.isEditable;
    }
});


export const newQuizAnswer = reactive({
    ...modal,
    show() {
        this.isShow = true;
        this.description = '';
        this.isCorrect = false;
    },
    description: '',
    isCorrect: false
});

export const removedQuizAnswer = reactive({
    ...modal,
    id: 0,
    description: ''
});

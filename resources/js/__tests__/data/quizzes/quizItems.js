import { quizzes } from '@/__tests__/data/quizzes/quizzes';

const quiz = quizzes[0];

export const quizItems = [{
    id: 1,
    description: "2 * 2 = ?",
    quiz_id: quiz.id,
    status: {
        name: "в работе",
        style: "status-sky",
        isEditable: true
    },
    quiz
}, {
    id: 2,
    description: "2 + 11 = ?",
    quiz_id: quiz.id,
    status: {
        name: "готов",
        style: "status-green",
        isEditable: true
    },
    quiz
}, {
    id: 3,
    description: "2 - 3 = ?",
    quiz_id: quiz.id,
    status: {
        name: "удалён",
        style: "status-gray",
        isEditable: false
    },
    quiz
}];

export const quizItemWithoutAnswers = {
    ...quizItems[0],
    answers: []
};

export const quizItemWithAnswers = {
    ...quizItems[0],
    answers: [{
            id: 1,
            description: "5",
            is_correct: false,
            quiz_item_id: quizItems[0].id
        }, {
            id: 2,
            description: "4",
            is_correct: true,
            quiz_item_id: quizItems[0].id
        }]
};

import { quizzes } from '@/__tests__/data/quizzes/quizzes';
import { quizItems } from '@/__tests__/data/quizzes/quizItems';

const quiz = quizzes[0];

export const trials = [{
        ...quizItems[0],
        id: 1,
        start_at_seconds: Math.floor(Date.now() / 1000),
        lead_time: 10,
        answers: [{
            id: 1,
            answer: 'Ответ с id = 1',
            quiz_item_id: quizItems[0].id
        }, {
            id: 2,
            answer: null,
            quiz_item_id: quizItems[0].id
        }, {
            id: 3,
            answer: 'Ответ с id = 3',
            quiz_item_id: quizItems[0].id
        }]
    }, {
        ...quizItems[1],
        id: 2,
        start_at_seconds: Math.floor(Date.now() / 1000),
        lead_time: 20,
        answers: [{
            id: 4,
            answer: 'Ответ с id = 4',
            quiz_item_id: quizItems[1].id
        }, {
            id: 5,
            answer: 'Ответ с id = 5',
            quiz_item_id: quizItems[1].id
        }, {
            id: 6,
            answer: 'Ответ с id = 6',
            quiz_item_id: quizItems[1].id
        }]
    }];

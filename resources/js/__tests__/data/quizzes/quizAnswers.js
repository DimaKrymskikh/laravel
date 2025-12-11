import { quizItems } from '@/__tests__/data/quizzes/quizItems';

const quizItem = quizItems[0];

export const answerFalse = {
    id: 1,
    description: "5",
    is_correct: false,
    quiz_item_id: quizItem.id,
    quiz_item: quizItem
};

export const answerTrue = {
    id: 2,
    description: "4",
    is_correct: true,
    priority: '2',
    quiz_item_id: quizItem.id,
    quiz_item: quizItem
};

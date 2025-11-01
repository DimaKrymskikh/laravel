import { newQuizItem } from '@/Services/Content/Quizzes/quizCard';

describe("@/Services/Content/Quizzes/quizCard", () => {
    // В начале каждого теста устанавливаем дефолтные значения
    beforeEach(() => {
        newQuizItem.description = '';
        newQuizItem.isShow = false;
    });
    
    it("newQuizItem.showAddQuizItemModal()", () => {
        newQuizItem.description = 'TestDescription';
        
        newQuizItem.show();
        expect(newQuizItem.description).toBe('');
        expect(newQuizItem.isShow).toBe(true);
    });
    
    it("newQuizItem.hideAddQuizItemModal", () => {
        newQuizItem.isShow = true;
        
        newQuizItem.hide();
        expect(newQuizItem.isShow).toBe(false);
    });
});

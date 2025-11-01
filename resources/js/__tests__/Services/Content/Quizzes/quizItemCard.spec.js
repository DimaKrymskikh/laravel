import { newQuizAnswer, removedQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';

describe("@/Services/Content/Quizzes/quizAnswerCard", () => {
    // В начале каждого теста устанавливаем дефолтные значения
    beforeEach(() => {
        newQuizAnswer.description = '';
        newQuizAnswer.isCorrect = false;
        newQuizAnswer.isShow = false;
        
        removedQuizAnswer.isShow = false;
    });
    
    it("newQuizAnswer.show", () => {
        newQuizAnswer.description = 'TestDescription';
        
        newQuizAnswer.show();
        expect(newQuizAnswer.description).toBe('');
        expect(newQuizAnswer.isShow).toBe(true);
    });
    
    it("newQuizAnswer.hide", () => {
        newQuizAnswer.isShow = true;
        
        newQuizAnswer.hide();
        expect(newQuizAnswer.isShow).toBe(false);
    });
    
    it("removedQuizAnswer.show", () => {
        removedQuizAnswer.show();
        expect(removedQuizAnswer.isShow).toBe(true);
    });
    
    it("removedQuizAnswer.hide", () => {
        removedQuizAnswer.isShow = true;
        
        removedQuizAnswer.hide();
        expect(removedQuizAnswer.isShow).toBe(false);
    });
});

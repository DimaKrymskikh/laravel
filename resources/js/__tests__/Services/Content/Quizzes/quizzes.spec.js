import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { newQuiz, updateQuiz } from '@/Services/Content/Quizzes/quizzes';

const defaultLeadTime = '20';

vi.mock('@inertiajs/vue3');

const appErrorRequest = vi.spyOn(app, 'errorRequest');
const updateQuizHide = vi.spyOn(updateQuiz, 'hide');

describe("@/Services/Content/Quizzes/quizzes", () => {
    // В начале каждого теста устанавливаем дефолтные значения
    beforeEach(() => {
        app.isRequest = false;
        
        newQuiz.id = 0;
        newQuiz.title = '';
        newQuiz.description = '';
        newQuiz.leadTime = '0';
        newQuiz.isShow = false;
        
        updateQuiz.errorsMessage = '';
        updateQuiz.isShow = false;
    });
    
    it("newQuiz.show", () => {
        newQuiz.title = 'TestTitle';
        newQuiz.description = 'TestDescription';
        newQuiz.leadTime = '777';
        
        newQuiz.show();
        expect(newQuiz.isShow).toBe(true);
        expect(newQuiz.title).toBe('');
        expect(newQuiz.description).toBe('');
        expect(newQuiz.leadTime).toBe(defaultLeadTime);
    });
    
    it("newQuiz.hide", () => {
        newQuiz.isShow = true;
        
        newQuiz.hide();
        expect(newQuiz.isShow).toBe(false);
    });
    
    it("updateQuiz.show", () => {
        updateQuiz.show();
        expect(updateQuiz.isShow).toBe(true);
    });
    
    it("updateQuiz.hide", () => {
        updateQuiz.isShow = true;
        
        updateQuiz.hide();
        expect(updateQuiz.isShow).toBe(false);
    });
    
    it("updateQuiz.onBefore", () => {
        updateQuiz.errorsMessage = 'Test Error Message';
        
        updateQuiz.onBefore();
        expect(updateQuiz.errorsMessage).toBe('');
        expect(app.isRequest).toBe(true);
    });
    
    it("updateQuiz.onSuccess", () => {
        updateQuiz.onSuccess();
        expect(updateQuizHide).toHaveBeenCalledTimes(1);
    });
    
    it("updateQuiz.onError с ошибкой для поля", () => {
        const updateQuizTitle = { ...updateQuiz, field: 'title' };
        
        const errorMessage = 'Fail Title';
        
        updateQuizTitle.onError({
            title: errorMessage
        });
        
        expect(updateQuizTitle.errorsMessage).toBe(errorMessage);
        expect(appErrorRequest).toHaveBeenCalledTimes(1);
        // Поле редактирования не закрывается
        expect(updateQuizHide).toHaveBeenCalledTimes(0);
    });
    
    it("updateQuiz.onError с глобальной ошибкой", () => {
        const errorMessage = 'Fail Message';
        
        updateQuiz.onError({
            message: errorMessage
        });
        
        expect(updateQuiz.errorsMessage).toBe('');
        expect(appErrorRequest).toHaveBeenCalledTimes(1);
        // Поле редактирования закрывается
        expect(updateQuizHide).toHaveBeenCalledTimes(1);
    });
});

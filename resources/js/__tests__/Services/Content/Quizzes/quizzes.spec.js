import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { newQuiz, activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';

const defaultLeadTime = '20';

vi.mock('@inertiajs/vue3');

const appErrorRequest = vi.spyOn(app, 'errorRequest');

const activeFieldId = '7';
const activeFieldField = 'field';
const activeFieldUrl = 'url';
const activeFieldModal = { ...fieldModal };
const modalHide = vi.spyOn(activeFieldModal, 'hide');

describe("@/Services/Content/Quizzes/quizzes", () => {
    // В начале каждого теста устанавливаем дефолтные значения
    beforeEach(() => {
        app.isRequest = false;
        
        newQuiz.id = 0;
        newQuiz.title = '';
        newQuiz.description = '';
        newQuiz.leadTime = '0';
        newQuiz.isShow = false;
        
        activeField.id = undefined;
        activeField.field = undefined;
        activeField.url = undefined;
        activeField.modal = null;
        
        fieldModal.isShow = false;
    });
    
    it("activeField.set", () => {
        activeField.set(activeFieldId, activeFieldField, activeFieldUrl, activeFieldModal);
        expect(activeField.id).toBe(activeFieldId);
        expect(activeField.field).toBe(activeFieldField);
        expect(activeField.url).toBe(activeFieldUrl);
        expect(activeField.modal).toStrictEqual(activeFieldModal);
    });
    
    it("activeField.reset", () => {
        activeField.id = activeFieldId;
        activeField.field = activeFieldId;
        activeField.url = activeFieldUrl;
        activeField.modal = activeFieldModal;
        
        activeField.reset();
        
        expect(activeField.id).toBe(undefined);
        expect(activeField.field).toBe(undefined);
        expect(activeField.url).toBe(undefined);
        expect(activeField.modal).toStrictEqual(null);
    });
    
    it("updateQuiz.onBefore", () => {
        activeField.errorsMessage = 'Test Error Message';
        
        activeField.onBefore();
        expect(activeField.errorsMessage).toBe('');
        expect(app.isRequest).toBe(true);
    });
    
    it("activeField.onSuccess", () => {
        activeField.modal = activeFieldModal;
        
        activeField.onSuccess();
        
        expect(activeField.modal).toStrictEqual(null);
    });
    
    it("activeField.onError с ошибкой для поля", () => {
        const errorMessage = 'Fail Title';
        activeField.modal = activeFieldModal;
        activeField.field = 'title';
        
        activeField.onError({
            title: errorMessage
        });
        
        expect(activeField.errorsMessage).toBe(errorMessage);
        expect(appErrorRequest).toHaveBeenCalledTimes(1);
        // Поле редактирования не закрывается
        expect(modalHide).toHaveBeenCalledTimes(0);
    });
    
    it("activeField.onError с глобальной ошибкой", () => {
        const errorMessage = 'Fail Message';
        activeField.modal = activeFieldModal;
        
        activeField.onError({
            message: errorMessage
        });
        
        expect(activeField.errorsMessage).toBe('');
        expect(appErrorRequest).toHaveBeenCalledTimes(1);
        // Поле редактирования закрывается
        expect(modalHide).toHaveBeenCalledTimes(1);
    });
    
    it("activeField.update", () => {
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        activeField.set(activeFieldId, activeFieldField, activeFieldUrl, activeFieldModal);
        
        activeField.update('Value');
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(activeFieldUrl, {
                field: activeFieldField,
                value: 'Value'
            }, options);
    });
    
    it("fieldModal.show (app.isRequest = false)", () => {
        fieldModal.show();
        
        expect(fieldModal.isShow).toBe(true);
    });
    
    it("fieldModal.show (app.isRequest = true)", () => {
        app.isRequest = true;
        fieldModal.show();
        
        expect(fieldModal.isShow).toBe(false);
    });
    
    it("fieldModal.hide", () => {
        fieldModal.isShow = true;
        fieldModal.hide();
        
        expect(fieldModal.isShow).toBe(false);
    });
    
    it("fieldModal.hideWithoutRequest", () => {
        fieldModal.isShow = true;
        fieldModal.hideWithoutRequest();
        
        expect(fieldModal.isShow).toBe(false);
    });
});

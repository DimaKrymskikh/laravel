import { app } from '@/Services/app';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from "@/Services/router.js";

const hide = () => {
    app.isRequest = false;
};

describe("@/Services/router.js", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Вызов defaultOnBefore", () => {
        defaultOnBefore();
        
        expect(app.isRequest).toBe(true); 
    });
    
    it("Вызов defaultOnError с сообщением об ошибке поля", () => {
        app.isRequest = true;
        defaultOnError(hide)({fieald: 'Test Error'});
        // app.isRequest не меняется
        expect(app.isRequest).toBe(true); 
    });
    
    it("Вызов defaultOnError с глобальной ошибкой", () => {
        app.isRequest = true;
        defaultOnError(hide)({message: 'Test Error'});
        // app.isRequest изменился
        expect(app.isRequest).toBe(false); 
    });
    
    it("Вызов defaultOnFinish", () => {
        app.isRequest = true;
        defaultOnFinish();
        
        expect(app.isRequest).toBe(false); 
    });
});

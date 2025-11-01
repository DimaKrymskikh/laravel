import { app } from '@/Services/app';

export const defaultOnBefore = () => { app.isRequest = true; };

export const defaultOnError = (hide) => (errors) => {
    app.errorRequest(errors);
    if(errors.message) {
        hide();
    }
};

export const defaultOnFinish = () => { app.isRequest = false; };

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import { useAppStore } from '@/Stores/app';

describe("@/components/Modal/ForbiddenModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты ForbiddenModal", async () => {
        const app = useAppStore();
        app.isShowForbiddenModal = true;
        app.errorMessage = 'Некоторая ошибка';
        
        const wrapper = mount(ForbiddenModal, {
            global: {
                provide: { app }
            }
        });

        // Модальное окно компоненты ForbiddenModal присутствует
        const forbiddenModal = wrapper.get('#forbidden-modal');
        expect(forbiddenModal.isVisible()).toBe(true);
        
        // Отображается сообщение об ошибке
        const errorMessage = forbiddenModal.get('#error-message');
        expect(errorMessage.text()).toBe('Некоторая ошибка');
        
        // Отображается кнопка 'Закрыть'
        const button = forbiddenModal.get('button');
        expect(button.text()).toBe('Закрыть');
        // После клика по этой кнопке модальное окно закрывается
        await button.trigger('click');
        expect(wrapper.find('#forbidden-modal').exists()).toBe(false);
        // В модели app сообщение об ошибке сбрасывается
        expect(app.errorMessage).toBe('');
    });
    
    it("ForbiddenModal отсутствует, если нет ошибки", async () => {
        const app = useAppStore();
        
        const wrapper = mount(ForbiddenModal, {
            global: {
                provide: { app }
            }
        });

        // Модальное окно компоненты ForbiddenModal отсутствует
        const forbiddenModal = wrapper.find('#forbidden-modal');
        expect(forbiddenModal.exists()).toBe(false);
    });
});

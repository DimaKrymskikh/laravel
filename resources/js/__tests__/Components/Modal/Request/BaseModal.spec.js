import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import BaseModal from '@/components/Modal/Request/BaseModal.vue';
import { useAppStore } from '@/Stores/app';

const getWrapper = function(app, hideModal) {
    return mount(BaseModal, {
            props: {
                headerTitle: 'Заголовок модального окна',
                hideModal,
                handlerSubmit: vi.fn()
            },
            global: {
                provide: { app }
            }
        });
};

describe("@/components/Modal/Request/BaseModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты BaseModal (isRequest: false)", async () => {
        const app = useAppStore();
        
        const hideModal = vi.fn();
        
        const wrapper = getWrapper(app, hideModal);

        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Заголовок модального окна');
        
        // Кнопка 'Да' не содержит класс 'disabled'
        expect(wrapper.get('#modal-yes').element.classList.contains('disabled')).toBe(false);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(modalNo.text()).toBe('Нет');
        expect(hideModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideModal).toHaveBeenCalled();
        
        hideModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideModal).toHaveBeenCalled();
        
        hideModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideModal).toHaveBeenCalled();
    });
    
    it("Монтирование компоненты BaseModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const hideModal = vi.fn();
        const handlerSubmit = vi.fn();
        
        const wrapper = getWrapper(app, hideModal);

        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Заголовок модального окна');
        
        // Кнопка 'Да' содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.classes('disabled')).toBe(true);
        
        // Клик по кнопке 'Нет' не закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(modalNo.text()).toBe('Нет');
        expect(hideModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideModal).not.toHaveBeenCalled();
        
        hideModal.mockClear();
        
        // Клик по крестику не закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideModal).not.toHaveBeenCalled();
        
        hideModal.mockClear();
        
        // Клик по заднему фону не закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideModal).not.toHaveBeenCalled();
    });
});

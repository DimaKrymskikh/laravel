import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import InputField from '@/components/Elements/InputField.vue';
import { filmsAccountStore } from '@/Stores/films';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/AdminModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    afterEach(async () => {
        await router.post.mockClear();
    });
    
    it("Монтирование компоненты AdminModal (admin: false)", async () => {
        const hideAdminModal = vi.fn();
        const filmsAccount = filmsAccountStore();

        const wrapper = mount(AdminModal, {
            props: {
                hideAdminModal,
                admin: false
            },
            global: {
                provide: { filmsAccount }
            }
        });
        
        // Проверка равенства переменных ref начальным данным
        expect(wrapper.vm.inputPassword).toBe('');
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(wrapper.vm.isRequest).toBe(false);

        // id модального окна задаётся
        expect(wrapper.get('#admin-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Подтверждение статуса админа');
        // Содержится вопрос
        expect(wrapper.text()).toContain('Вы хотите получить права админа?');
        // Присутствуют название поля
        expect(wrapper.text()).toContain('Введите пароль:');
        
        // Поле ввода пароля заполняется
        const inputField = wrapper.findComponent(InputField);
        const input = inputField.get('input');
        input.setValue('TestPassword');
        expect(input.element.value).toBe('TestPassword');
        expect(wrapper.vm.inputPassword).toBe('TestPassword');
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(hideAdminModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideAdminModal).toHaveBeenCalledTimes(1);
        
        hideAdminModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideAdminModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideAdminModal).toHaveBeenCalledTimes(1);
        
        hideAdminModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideAdminModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideAdminModal).toHaveBeenCalledTimes(1);
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
    });
    
    it("Монтирование компоненты AdminModal (admin: true)", async () => {
        const hideAdminModal = vi.fn();
        const filmsAccount = filmsAccountStore();

        const wrapper = mount(AdminModal, {
            props: {
                hideAdminModal,
                admin: true
            },
            global: {
                provide: { filmsAccount }
            }
        });
        
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Отказ от статуса админа');
        // Содержится вопрос
        expect(wrapper.text()).toContain('Вы хотите отказаться от статуса админа?');
        // Присутствуют название поля
        expect(wrapper.text()).toContain('Введите пароль:');
    });
});

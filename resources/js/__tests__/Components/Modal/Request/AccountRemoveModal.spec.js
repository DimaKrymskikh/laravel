import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';
import InputField from '@/components/Elements/InputField.vue';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/AccountRemoveModal.vue", () => {
    afterEach(async () => {
        await router.delete.mockClear();
    });
    
    it("Монтирование компоненты AccountRemoveModal (isRequest: false)", async () => {
        const hideAccountRemoveModal = vi.fn();

        const wrapper = mount(AccountRemoveModal, {
            props: {
                hideAccountRemoveModal
            }
        });
        
        // Проверка равенства переменных ref начальным данным
        expect(wrapper.vm.inputPassword).toBe('');
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(wrapper.vm.isRequest).toBe(false);

        // id модального окна задаётся
        expect(wrapper.get('#account-remove-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Подтверждение удаления аккаунта');
        // Содержится вопрос
        expect(wrapper.text()).toContain('Вы действительно хотите удалить свой аккаунт?');
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
        expect(hideAccountRemoveModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideAccountRemoveModal).toHaveBeenCalledTimes(1);
        
        hideAccountRemoveModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideAccountRemoveModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideAccountRemoveModal).toHaveBeenCalledTimes(1);
        
        hideAccountRemoveModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideAccountRemoveModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideAccountRemoveModal).toHaveBeenCalledTimes(1);
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.delete).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.delete).toHaveBeenCalledTimes(1);
    });
});

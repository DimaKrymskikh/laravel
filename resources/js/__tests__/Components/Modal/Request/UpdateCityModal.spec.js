import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import UpdateCityModal from '@/Components/Modal/Request/UpdateCityModal.vue';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/UpdateCityModal.vue", () => {
    afterEach(async () => {
        await router.put.mockClear();
    });
    
    it("Монтирование компоненты UpdateCityModal (isRequest: false)", async () => {
        const hideUpdateCityModal = vi.fn();

        const wrapper = mount(UpdateCityModal, {
            props: {
                hideUpdateCityModal,
                updateCity: {
                    id: '5',
                    name: 'Город'
                }
            }
        });
        
        // Проверка равенства переменных ref начальным данным
        expect(wrapper.vm.cityName).toBe('Город');
        expect(wrapper.vm.errorsName).toBe('');
        expect(wrapper.vm.isRequest).toBe(false);

        // id модального окна задаётся
        expect(wrapper.get('#update-city-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Изменение названия города');
        // Присутствует поле для названия города
        expect(wrapper.text()).toContain('Название города:');
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(hideUpdateCityModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideUpdateCityModal).toHaveBeenCalled();
        
        hideUpdateCityModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideUpdateCityModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideUpdateCityModal).toHaveBeenCalled();
        
        hideUpdateCityModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideUpdateCityModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideUpdateCityModal).toHaveBeenCalled();
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.put).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.put).toHaveBeenCalled();
    });
});

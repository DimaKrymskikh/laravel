import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import RemoveCityModal from '@/Components/Modal/Request/RemoveCityModal.vue';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/RemoveCityModal.vue", () => {
    afterEach(async () => {
        await router.delete.mockClear();
    });
    
    it("Монтирование компоненты RemoveCityModal (isRequest: false)", async () => {
        const hideRemoveCityModal = vi.fn();
        
        const cityName = 'Несуществующий город';
        const cityOpenWeatherId = '13700';

        const wrapper = mount(RemoveCityModal, {
            props: {
                hideRemoveCityModal,
                removeCity: {
                    id: 7,
                    name: cityName,
                    open_weather_id: cityOpenWeatherId
                }
            }
        });
        
        // Проверка равенства переменных ref начальным данным
        expect(wrapper.vm.inputPassword).toBe('');
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(wrapper.vm.isRequest).toBe(false);

        // id модального окна задаётся
        expect(wrapper.get('#remove-city-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Подтверждение удаления города');
        // Содержится вопрос модального окна с нужными параметрами
        expect(wrapper.text()).toContain('Вы действительно хотите удалить город');
        expect(wrapper.text()).toContain(cityName);
        expect(wrapper.text()).toContain(cityOpenWeatherId);
        // Присутствует поле для ввода пароля
        expect(wrapper.text()).toContain('Введите пароль:');
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(hideRemoveCityModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideRemoveCityModal).toHaveBeenCalled();
        
        hideRemoveCityModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideRemoveCityModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideRemoveCityModal).toHaveBeenCalled();
        
        hideRemoveCityModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideRemoveCityModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideRemoveCityModal).toHaveBeenCalled();
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.delete).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.delete).toHaveBeenCalled();
    });
});

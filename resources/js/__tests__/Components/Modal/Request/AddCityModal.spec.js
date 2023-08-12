import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import AddCityModal from '@/Components/Modal/Request/AddCityModal.vue';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/AddCityModal.vue", () => {
    afterEach(async () => {
        await router.post.mockClear();
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: false)", async () => {
        const hideAddCityModal = vi.fn();

        const wrapper = mount(AddCityModal, {
            props: {
                hideAddCityModal
            }
        });
        
        // Проверка равенства переменных ref начальным данным
        expect(wrapper.vm.cityName).toBe('');
        expect(wrapper.vm.openWeatherId).toBe('');
        expect(wrapper.vm.errorsName).toBe('');
        expect(wrapper.vm.errorsOpenWeatherId).toBe('');
        expect(wrapper.vm.isRequest).toBe(false);

        // id модального окна задаётся
        expect(wrapper.get('#add-city-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Добавление города');
        // Присутствуют названия полей
        expect(wrapper.text()).toContain('Имя города:');
        expect(wrapper.text()).toContain('Id города в OpenWeather:');
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        expect(hideAddCityModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideAddCityModal).toHaveBeenCalled();
        
        hideAddCityModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideAddCityModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideAddCityModal).toHaveBeenCalled();
        
        hideAddCityModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideAddCityModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideAddCityModal).toHaveBeenCalled();
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.post).toHaveBeenCalled();
    });
});

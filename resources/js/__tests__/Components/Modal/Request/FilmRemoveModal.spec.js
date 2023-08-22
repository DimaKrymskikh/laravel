import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import InputField from '@/components/Elements/InputField.vue';
import { filmsAccountStore } from '@/Stores/films';

import { films_10 } from '@/__tests__/data/films';

vi.mock('@inertiajs/vue3');
        
describe("@/Components/Modal/Request/FilmRemoveModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    afterEach(async () => {
        await router.delete.mockClear();
    });
    
    it("Монтирование компоненты FilmRemoveModal (isRequest: false)", async () => {
        const hideFilmRemoveModal = vi.fn();
        const filmsAccount = filmsAccountStore();

        const wrapper = mount(FilmRemoveModal, {
            props: {
                films: films_10,
                removeFilmTitle: 'Attraction Newton',
                removeFilmId: '45',
                hideFilmRemoveModal
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
        expect(wrapper.get('#film-remove-modal').isVisible()).toBe(true);
        // Заголовок модального окна задаётся
        expect(wrapper.text()).toContain('Подтверждение удаления фильма');
        // Содержится вопрос
        expect(wrapper.text()).toContain('Вы действительно хотите удалить фильм');
        expect(wrapper.text()).toContain('Attraction Newton');
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
        expect(hideFilmRemoveModal).not.toHaveBeenCalled();
        await modalNo.trigger('click');
        expect(hideFilmRemoveModal).toHaveBeenCalledTimes(1);
        
        hideFilmRemoveModal.mockClear();
        
        // Клик по крестику закрывает модальное окно
        const modalCross = wrapper.get('#modal-cross');
        expect(hideFilmRemoveModal).not.toHaveBeenCalled();
        await modalCross.trigger('click');
        expect(hideFilmRemoveModal).toHaveBeenCalledTimes(1);
        
        hideFilmRemoveModal.mockClear();
        
        // Клик по заднему фону закрывает модальное окно
        const modalBackground = wrapper.get('#modal-background');
        expect(hideFilmRemoveModal).not.toHaveBeenCalled();
        await modalBackground.trigger('click');
        expect(hideFilmRemoveModal).toHaveBeenCalledTimes(1);
        
        // Кнопка 'Да' не содержит класс 'disabled'
        const modalYes = wrapper.get('#modal-yes');
        expect(modalYes.element.classList.contains('disabled')).toBe(false);
        // Клик по кнопке 'Да' отправляет запрос на сервер
        expect(router.delete).not.toHaveBeenCalled();
        await modalYes.trigger('click');
        expect(router.delete).toHaveBeenCalledTimes(1);
    });
});

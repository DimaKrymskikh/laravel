import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

import { films_10 } from '@/__tests__/data/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideFilmRemoveModal = vi.fn();

const getWrapper = function(app, filmsAccount) {
    return mount(FilmRemoveModal, {
            props: {
                films: films_10,
                removeFilmTitle: 'Attraction Newton',
                removeFilmId: '45',
                hideFilmRemoveModal
            },
            global: {
                provide: { app, filmsAccount }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления фильма');
    // Содержится вопрос
    expect(wrapper.text()).toContain('Вы действительно хотите удалить фильм');
    expect(wrapper.text()).toContain('Attraction Newton');
};
        
describe("@/Components/Modal/Request/FilmRemoveModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты FilmRemoveModal (isRequest: false)", async () => {
        const app = useAppStore();
        
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, 'Подтверждение удаления фильма', hideFilmRemoveModal, wrapper.vm.handlerRemoveFilm
            );
        checkBaseModal.presenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, hideFilmRemoveModal);
        await checkBaseModal.submitRequestInBaseModal(baseModal, router.delete);
    });
    
    it("Монтирование компоненты FilmRemoveModal (isRequest: true)", async () => {
        const app = useAppStore();
        // Выполняется запрос
        app.isRequest = true;
        
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideFilmRemoveModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
});

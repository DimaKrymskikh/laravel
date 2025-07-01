import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import { useFilmsAccountStore } from '@/Stores/films';
import { app } from '@/Services/app';

import { films_10 } from '@/__tests__/data/films';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideFilmRemoveModal = vi.fn();

const getWrapper = function() {
    return mount(FilmRemoveModal, {
            props: {
                films: films_10,
                removeFilmTitle: 'Attraction Newton',
                removeFilmId: '45',
                hideFilmRemoveModal
            },
            global: {
                provide: {
                    filmsAccount: useFilmsAccountStore()
                }
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
        const wrapper = getWrapper();
        
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
        // Выполняется запрос
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideFilmRemoveModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveFilm вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        const options = {
            data: {
                password: wrapper.vm.inputPassword
            },
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(wrapper.vm.filmsAccount.getUrl(`userfilms/removefilm/${wrapper.vm.removeFilmId}`), options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveFilm", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveFilm", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.filmsAccount.page).toBe(1);
        expect(hideFilmRemoveModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveFilm({props: {films: films_10}});
        
        expect(hideFilmRemoveModal).toHaveBeenCalledTimes(1);
        expect(hideFilmRemoveModal).toHaveBeenCalledWith();
        expect(wrapper.vm.filmsAccount.page).toBe(films_10.current_page);
        expect(films_10.current_page).toBe(5);
    });
    
    it("Проверка функции onErrorForHandlerRemoveFilm ({ password: 'ErrorPassword' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveFilm({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
        expect(app.errorMessage).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
    });
    
    it("Проверка функции onErrorForHandlerRemoveFilm ({ message: 'ServerError' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveFilm({ message: 'ServerError' });
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.errorMessage).toBe('ServerError');
        expect(app.isShowForbiddenModal).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRemoveFilm", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerRemoveFilm();
        expect(app.isRequest).toBe(false);
    });
});

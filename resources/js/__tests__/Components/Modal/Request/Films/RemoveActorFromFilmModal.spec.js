import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { film, removeActor } from '@/Services/Content/films';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(removeActor, 'hideRemoveActorFromFilmModal');

const getWrapper = function() {
    return mount(RemoveActorFromFilmModal, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления актёра из фильма');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain(`Вы действительно хотите удалить актёра ${removeActor.firstName} ${removeActor.lastName}`);
    expect(wrapper.text()).toContain(`из фильма ${film.title}`);
};

describe("@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveActorFromFilmModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveActorFromFilmModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveActorFromFilm вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        const options = {
            data: {
                password: wrapper.vm.inputPassword,
                film_id: film.id
            },
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveActorFromFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/actors/${removeActor.id}`), options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveActorFromFilm", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveActorFromFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveActorFromFilm", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveActorFromFilm();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveActorFromFilm", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerRemoveActorFromFilm({password: 'ErrorPassword'});
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerRemoveActorFromFilm", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onFinishForHandlerRemoveActorFromFilm();
        
        expect(app.isRequest).toBe(false);
    });
});

import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import RemoveFilmModal from '@/Components/Modal/Request/Films/RemoveFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';
import { updateFilm } from '@/Services/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideRemoveFilmModal = vi.fn();

function setUpdateFilm() {
    updateFilm.id = 25;
    updateFilm.title = 'Бриллиантовая рука';
};

const getWrapper = function(app) {
    return mount(RemoveFilmModal, {
            props: {
                hideRemoveFilmModal
            },
            global: {
                provide: {
                    app,
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
    expect(wrapper.text()).toContain('Подтверждение удаления фильма');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain(`Вы действительно хотите удалить фильм ${updateFilm.title}`);
};

describe("@/Components/Modal/Request/Films/RemoveFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveFilmModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveFilmModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveFilm вызывает router.delete с нужными параметрами", () => {
        const app = useAppStore();
        
        setUpdateFilm();

        const wrapper = getWrapper(app);
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
        expect(router.delete).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/${updateFilm.id}`), options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveFilm", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(hideRemoveFilmModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveFilm();
        
        expect(hideRemoveFilmModal).toHaveBeenCalledTimes(1);
        expect(hideRemoveFilmModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveFilm ({password: 'ErrorPassword'})", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveFilm({password: 'ErrorPassword'});
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
        expect(app.errorMessage).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
    });
    
    it("Проверка функции onErrorForHandlerRemoveFilm ({ message: 'ServerError' })", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveFilm({ message: 'ServerError' });
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.errorMessage).toBe('ServerError');
        expect(app.isShowForbiddenModal).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRemoveFilm", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerRemoveFilm();
        
        expect(app.isRequest).toBe(false);
    });
});

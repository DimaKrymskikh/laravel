import { mount, flushPromises } from "@vue/test-utils";

import ResetPassword from "@/Pages/Guest/ResetPassword.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import InputField from '@/components/Elements/InputField.vue';
import { app } from '@/Services/app';

import { checkFormButton } from '@/__tests__/methods/checkFormButton';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { GuestLayoutStub } from '@/__tests__/stubs/layout';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(status = null) {
    return mount(ResetPassword, {
            props: {
                email: 'test@example.com',
                token: 'testtoken',
                status,
                errors: {}
            },
            global: {
                stubs: {
                    GuestLayout: GuestLayoutStub
                }
            }
        });
};

// Проверка названия страницы
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Сброс пароля');
};

// Проверка хлебных крошек
const checkBreadCrumb = function(wrapper) {
    // Отрисовываются хлебные крошки
    const breadCrumb = wrapper.findComponent(BreadCrumb);
    expect(breadCrumb.exists()).toBe(true);
    // Проверяем хлебные крошки
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(3);
    // Ссылка на страницу 'Главная страница'
    const a0 = li[0].find('a');
    expect(a0.attributes('href')).toBe('/guest');
    expect(a0.text()).toBe('Главная страница');
    // Ссылка на страницу 'Вход'
    const a1 = li[1].find('a');
    expect(a1.attributes('href')).toBe('/login');
    expect(a1.text()).toBe('Вход');
    // Название текущей страницы
    expect(li[2].find('a').exists()).toBe(false);
    expect(li[2].text()).toBe('Сброс пароля');
};

describe("@/Pages/Guest/ResetPassword.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Отрисовка формы сброса пароля (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        expect(wrapper.vm.form.token).toBe('testtoken');
        expect(wrapper.vm.form.email).toBe('test@example.com');
        expect(wrapper.vm.form.password).toBe('');
        expect(wrapper.vm.form.password_confirmation).toBe('');
        expect(wrapper.vm.form.errors).toStrictEqual({});
        
        expect(wrapper.find('#reset-password-status').exists()).toBe(false);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 3);
        checkInputField.checkPropsInputField(inputFields[0], 'Электронная почта:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email, undefined, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password, true);
        checkInputField.checkPropsInputField(
                inputFields[2], 'Подтверждение пароля:', 'password', wrapper.vm.form.errors.password_confirmation, wrapper.vm.form.password_confirmation
        );

        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], '', 'TestPassword');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[2], '', 'TestPassword');
        
        checkFormButton.checkPropsFormButton(wrapper, 'Задать новый пароль', 'w-48');
        await checkFormButton.submitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка формы сброса пароля (isRequest: true)", async () => {
        // Выполняется запрос
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        expect(wrapper.find('#reset-password-status').exists()).toBe(false);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 3);
        checkInputField.checkPropsInputField(inputFields[0], 'Электронная почта:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email, undefined, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password, true);
        checkInputField.checkPropsInputField(
                inputFields[2], 'Подтверждение пароля:', 'password', wrapper.vm.form.errors.password_confirmation, wrapper.vm.form.password_confirmation
        );

        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], '', 'TestPassword');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[2], '', 'TestPassword');
        
        checkFormButton.checkPropsFormButton(wrapper, 'Задать новый пароль', 'w-48');
        await checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка статуса", () => {
        const wrapper = getWrapper('Некоторый статус');
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const passwordStatus = wrapper.find('#reset-password-status');
        expect(passwordStatus.exists()).toBe(true);
        expect(passwordStatus.text()).toBe('Некоторый статус');
    });
    
    it("Функция handlerResetPassword вызывает form.post с нужными параметрами", () => {
        const options = {
            onBefore: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        wrapper.vm.handlerResetPassword();
        
        expect(formPost).toHaveBeenCalledTimes(1);
        expect(formPost).toHaveBeenCalledWith('/reset-password', options);
    });
    
    it("Проверка функции onBeforeForHandlerResetPassword", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.onBeforeForHandlerResetPassword();
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerResetPassword", async () => {
        const wrapper = getWrapper();
        
        // На странице две компоненты InputField
        const inputField = wrapper.findAllComponents(InputField);
        expect(inputField.length).toBe(3);
        
        // Находим поле для заполнения пароля 
        const passwordInput = inputField[1].get('input');
        // В начальный момент поле пароля - пустая строка
        expect(passwordInput.element.value).toBe('');
        // Устанавливаем новое значение (должно быть app.isRequest = false)
        passwordInput.setValue('TestPassword');
        // Проверяем, что величины изменились
        expect(passwordInput.element.value).toBe('TestPassword');
        expect(wrapper.vm.form.password).toBe('TestPassword');
        
        // Находим поле для подтверждения пароля 
        const passwordConfirmationInput = inputField[2].get('input');
        // В начальный момент поле пароля - пустая строка
        expect(passwordConfirmationInput.element.value).toBe('');
        // Устанавливаем новое значение (должно быть app.isRequest = false)
        passwordConfirmationInput.setValue('TestPasswordFail');
        // Проверяем, что величины изменились
        expect(passwordConfirmationInput.element.value).toBe('TestPasswordFail');
        expect(wrapper.vm.form.password_confirmation).toBe('TestPasswordFail');
        
        // Изменяем app.isRequest
        app.isRequest = true;
        wrapper.vm.onFinishForHandlerResetPassword();
        
        await flushPromises();
        
        // Величины вернулись в исходное состояние
        expect(app.isRequest).toBe(false);
        expect(passwordInput.element.value).toBe('');
        expect(wrapper.vm.form.password).toBe('');
        expect(passwordConfirmationInput.element.value).toBe('');
        expect(wrapper.vm.form.password_confirmation).toBe('');
    });
    
    it("Проверка функции onErrorForHandlerResetPassword", async () => {
        // Значения по умолчанию
        expect(app.isShowForbiddenModal).toBe(false);
        expect(app.errorMessage).toBe('');
        
        const wrapper = getWrapper();
        wrapper.vm.onErrorForHandlerResetPassword({message: "Токен сброса пароля недействителен."});
        
        await flushPromises();
        
        // Значения изменились
        expect(app.isShowForbiddenModal).toBe(true);
        expect(app.errorMessage).toBe("Токен сброса пароля недействителен.");
    });
});

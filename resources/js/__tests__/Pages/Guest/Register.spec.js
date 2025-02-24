import { mount, flushPromises } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Register from "@/Pages/Guest/Register.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import InputField from '@/components/Elements/InputField.vue';
import { useAppStore } from '@/Stores/app';

import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';
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

const getWrapper = function(app) {
    return mount(Register, {
            props: {
                errors: {}
            },
            global: {
                stubs: {
                    GuestLayout: GuestLayoutStub
                },
                provide: { app }
            }
        });
};

// Проверка названия страницы
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Регистрация');
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
    expect(li[2].text()).toBe('Регистрация');
};

describe("@/Pages/Guest/Register.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы регистрации (isRequest: false)", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // Начальное состояние формы
        expect(wrapper.vm.form.login).toBe('');
        expect(wrapper.vm.form.email).toBe('');
        expect(wrapper.vm.form.password).toBe('');
        expect(wrapper.vm.form.password_confirmation).toBe('');
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 4);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите логин:', 'text', wrapper.vm.form.errors.login, wrapper.vm.form.login, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите электронную почту:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email);
        checkInputField.checkPropsInputField(inputFields[2], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password);
        checkInputField.checkPropsInputField(
                inputFields[3], 'Подтверждение пароля:', 'password', wrapper.vm.form.errors.password_confirmation, wrapper.vm.form.password_confirmation
        );

        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], '', 'TestLogin');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], '', 'test@example.com');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[2], '', 'TestPassword');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[3], '', 'TestPassword');
        
        const checkbox = formTag.findComponent(Checkbox);
        testCheckbox.existsRememberMe(checkbox);
        testCheckbox.successChecked(checkbox);
        
        checkFormButton.checkPropsFormButton(wrapper, 'Зарегистрироваться', 'w-48');
        checkFormButton.submitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка формы регистрации (isRequest: true)", async () => {
        const app = useAppStore();
        // Выполняется запрос
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 4);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите логин:', 'text', wrapper.vm.form.errors.login, wrapper.vm.form.login, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите электронную почту:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email);
        checkInputField.checkPropsInputField(inputFields[2], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password);
        checkInputField.checkPropsInputField(
                inputFields[3], 'Подтверждение пароля:', 'password', wrapper.vm.form.errors.password_confirmation, wrapper.vm.form.password_confirmation
        );

        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], '', 'TestLogin');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], '', 'test@example.com');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[2], '', 'TestPassword');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[3], '', 'TestPassword');
        
        const checkbox = formTag.findComponent(Checkbox);
        testCheckbox.existsRememberMe(checkbox);
        testCheckbox.failChecked(checkbox);
        
        checkFormButton.checkPropsFormButton(wrapper, 'Зарегистрироваться', 'w-48');
        checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
    
    it("Функция handlerRegister вызывает form.post с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            onBefore: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        wrapper.vm.handlerRegister();
        
        expect(formPost).toHaveBeenCalledTimes(1);
        expect(formPost).toHaveBeenCalledWith('/register', options);
    });
    
    it("Проверка функции onBeforeForHandlerRegister", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.onBeforeForHandlerRegister();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRegister", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        // На странице две компоненты InputField
        const inputField = wrapper.findAllComponents(InputField);
        expect(inputField.length).toBe(4);
        
        // Находим поле для заполнения пароля 
        const passwordInput = inputField[2].get('input');
        // В начальный момент поле пароля - пустая строка
        expect(passwordInput.element.value).toBe('');
        // Устанавливаем новое значение (должно быть app.isRequest = false)
        passwordInput.setValue('TestPassword');
        // Проверяем, что величины изменились
        expect(passwordInput.element.value).toBe('TestPassword');
        expect(wrapper.vm.form.password).toBe('TestPassword');
        
        // Находим поле для подтверждения пароля 
        const passwordConfirmationInput = inputField[3].get('input');
        // В начальный момент поле пароля - пустая строка
        expect(passwordConfirmationInput.element.value).toBe('');
        // Устанавливаем новое значение (должно быть app.isRequest = false)
        passwordConfirmationInput.setValue('TestPasswordFail');
        // Проверяем, что величины изменились
        expect(passwordConfirmationInput.element.value).toBe('TestPasswordFail');
        expect(wrapper.vm.form.password_confirmation).toBe('TestPasswordFail');
        
        // Изменяем app.isRequest
        app.isRequest = true;
        wrapper.vm.onFinishForHandlerRegister();
        
        await flushPromises();
        
        // Величины вернулись в исходное состояние
        expect(app.isRequest).toBe(false);
        expect(passwordInput.element.value).toBe('');
        expect(wrapper.vm.form.password).toBe('');
        expect(passwordConfirmationInput.element.value).toBe('');
        expect(wrapper.vm.form.password_confirmation).toBe('');
    });
});

import { mount } from "@vue/test-utils";

import ForgotPassword from "@/Pages/Guest/ForgotPassword.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
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
    return mount(ForgotPassword, {
            props: {
                errors: {},
                status
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

describe("@/Pages/Guest/ForgotPassword.vue", () => {
    it("Отрисовка формы для сброса пароля (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // Начальное состояние формы
        expect(wrapper.vm.form.email).toBe(null);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        expect(wrapper.find('#forgot-password-status').exists()).toBe(false);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите электронную почту:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], '', 'test@example.com');
        
        checkFormButton.checkPropsFormButton(wrapper, 'Ссылка для сброса пароля электронной почты', 'w-96');
        await checkFormButton.submitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка формы для сброса пароля (isRequest: false)", async () => {
        // Выполняется запрос
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        expect(wrapper.find('#forgot-password-status').exists()).toBe(false);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите электронную почту:', 'text', wrapper.vm.form.errors.email, wrapper.vm.form.email, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], '', 'test@example.com');
        
        checkFormButton.checkPropsFormButton(wrapper, 'Ссылка для сброса пароля электронной почты', 'w-96');
        await checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка статуса", () => {
        const wrapper = getWrapper('Некоторый статус');
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        expect(wrapper.find('#forgot-password-status').exists()).toBe(true);
    });
    
    it("Функция handlerForgotPassword вызывает form.post с нужными параметрами", () => {
        const options = {
            onBefore: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        wrapper.vm.handlerForgotPassword();
        
        expect(formPost).toHaveBeenCalledTimes(1);
        expect(formPost).toHaveBeenCalledWith('/forgot-password', options);
    });
    
    it("Проверка функции onBeforeForForgotPassword", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.onBeforeForForgotPassword();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForForgotPassword", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        
        wrapper.vm.onFinishForForgotPassword();
        
        expect(app.isRequest).toBe(false);
    });
});

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Register from "@/Pages/Guest/Register.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { useAppStore } from '@/Stores/app';

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
        expect(wrapper.vm.form.login).toBe(null);
        expect(wrapper.vm.form.email).toBe(null);
        expect(wrapper.vm.form.password).toBe(null);
        expect(wrapper.vm.form.password_confirmation).toBe(null);
        
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
        
        checkFormButton.checkPropsFormButton(wrapper, 'Зарегистрироваться', 'w-48');
        checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
});

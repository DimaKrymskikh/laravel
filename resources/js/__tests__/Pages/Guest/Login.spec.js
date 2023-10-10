import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Login from "@/Pages/Guest/Login.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore } from '@/Stores/films';

import { checkFormButton } from '@/__tests__/methods/checkFormButton';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(app, filmsList, status = null) {
    return mount(Login, {
            props: {
                status
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Login'
                    }
                },
                provide: { app, filmsList }
            }
        });
};

// Проверка названия страницы
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Вход');
};

// Проверка хлебных крошек
const checkBreadCrumb = function(wrapper) {
    // Отрисовываются хлебные крошки
    const breadCrumb = wrapper.findComponent(BreadCrumb);
    expect(breadCrumb.exists()).toBe(true);
    // Проверяем хлебные крошки
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    // Первая крошка - это ссылка на главную страницу
    const a0 = li[0].find('a');
    expect(a0.attributes('href')).toBe('/guest');
    expect(a0.text()).toBe('Главная страница');
    // Вторая крошка ссылкой не является
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe('Вход');
};

describe("@/Pages/Guest/Login.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы входа (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        // Начальное состояние формы
        expect(wrapper.vm.form.login).toBe(null);
        expect(wrapper.vm.form.password).toBe(null);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        expect(wrapper.find('#login-status').exists()).toBe(false);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите логин:', 'text', wrapper.vm.form.errors.login, wrapper.vm.form.login, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], '', 'TestLogin');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], '', 'TestPassword');
        
        // Проверяем кнопку формы
        checkFormButton.checkPropsFormButton(wrapper, 'Вход', 'w-36');
        await checkFormButton.submitFormButton(wrapper, formPost);
        
        // На странице имеется ссылка 'Регистрация'
        const register = wrapper.get('a[href="/register"]');
        expect(register.text()).toBe('Регистрация');
        
        // На странице имеется ссылка 'Сброс пароля'
        const forgotPassword = wrapper.get('a[href="/forgot-password"]');
        expect(forgotPassword.text()).toBe('Сброс пароля');
    });
    
    it("Отрисовка формы входа (isRequest: true)", async () => {
        const app = useAppStore();
        // Выполняется запрос
        app.isRequest = true;
        
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(formTag, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите логин:', 'text', wrapper.vm.form.errors.login, wrapper.vm.form.login, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Введите пароль:', 'password', wrapper.vm.form.errors.password, wrapper.vm.form.password);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], '', 'TestLogin');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], '', 'TestPassword');
        
        // Проверяем кнопку формы
        checkFormButton.checkPropsFormButton(wrapper, 'Вход', 'w-36');
        await checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка статуса", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList, 'Некоторый статус');
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const loginStatus = wrapper.find('#login-status');
        expect(loginStatus.exists()).toBe(true);
        expect(loginStatus.text()).toBe('Некоторый статус');
    });
});

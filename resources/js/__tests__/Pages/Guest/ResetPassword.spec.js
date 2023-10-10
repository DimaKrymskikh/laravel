import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import ResetPassword from "@/Pages/Guest/ResetPassword.vue";
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
    return mount(ResetPassword, {
            props: {
                email: 'test@example.com',
                token: 'testtoken',
                status
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ResetPassword'
                    }
                },
                provide: { app, filmsList }
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
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы сброса пароля (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
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
        const app = useAppStore();
        // Выполняется запрос
        app.isRequest = true;
        
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
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
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList, 'Некоторый статус');
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        const passwordStatus = wrapper.find('#reset-password-status');
        expect(passwordStatus.exists()).toBe(true);
        expect(passwordStatus.text()).toBe('Некоторый статус');
    });
});

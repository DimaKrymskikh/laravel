import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import ForgotPassword from "@/Pages/Guest/ForgotPassword.vue";
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

const getWrapper = function(app, filmsList, status) {
    return mount(ForgotPassword, {
            props: {
                status
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ForgotPassword'
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

describe("@/Pages/Guest/ForgotPassword.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы для сброса пароля (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
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
        const app = useAppStore();
        // Выполняется запрос
        app.isRequest = true;
        
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
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
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(app, filmsList, 'Некоторый статус');
        
        expect(wrapper.find('#forgot-password-status').exists()).toBe(true);
    });
});
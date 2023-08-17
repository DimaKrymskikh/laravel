import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import ForgotPassword from "@/Pages/Guest/ForgotPassword.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { filmsCatalogStore } from '@/Stores/films';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Guest/ForgotPassword.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы для сброса пароля", () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(ForgotPassword, {
            props: {
                errors: null,
                status: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ForgotPassword'
                    }
                },
                provide: { filmsCatalog }
            }
        });
        
        expect(wrapper.vm.form.email).toBe(null);
        expect(wrapper.vm.form.processing).toBe(false);
        expect(wrapper.vm.isRequest).toBe(false);
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Сброс пароля');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Проверяем хлебные крошки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(3);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[1].find('a[href="/login"]').exists()).toBe(true);
        expect(li[1].text()).toBe('Вход');
        expect(li[2].find('a').exists()).toBe(false);
        expect(li[2].text()).toBe('Сброс пароля');
        
        expect(wrapper.find('#forgot-password-status').exists()).toBe(false);
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const label = formTag.findAll('label');
        expect(label.length).toBe(1);
        expect(label[0].text()).toBe('Электронная почта:');
        
        const input = formTag.findAll('input');
        expect(input.length).toBe(1);
        expect(input[0].element.value).toBe('');
        
        input[0].setValue('test@example.com');
        expect(input[0].element.value).toBe('test@example.com');
        expect(wrapper.vm.form.email).toBe('test@example.com');
        
        const error = formTag.findAll('.error');
        expect(error.length).toBe(0);
        
        const formButton = formTag.getComponent(FormButton);
        expect(formButton.isVisible()).toBe(true);
        
        const button = formButton.get('button');
        expect(button.isVisible()).toBe(true);
        
        // Существует проблема при тестировании
        // Когда wrapper.vm.form.processing = false 
        // появляется 
        // [Vue warn]: Failed setting prop "disabled" on <button>: value false is invalid. TypeError: Cannot read properties of null (reading 'name')
        expect(button.attributes('disabled')).toBe(undefined);

        // На кнопке отправки формы виден текст, а спиннер отсутствует
        expect(button.find('span').text()).toBe('Ссылка для сброса пароля электронной почты');
        expect(button.findComponent(Spinner).exists()).toBe(false);
    });
    
    it("Отправка формы для сброса пароля", async () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(ForgotPassword, {
            props: {
                errors: null,
                status: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ForgotPassword'
                    }
                },
                provide: { filmsCatalog }
            }
        });
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        const formTag = wrapper.get('form');
        const input = formTag.findAll('input');
        input[0].setValue('test@example.com');
        
        const formButton = formTag.getComponent(FormButton);
        const button = formButton.get('button');
        
        expect(formPost).not.toHaveBeenCalled();
        await button.trigger('submit');
        expect(formPost).toHaveBeenCalledTimes(1);
    });
});

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import ResetPassword from "@/Pages/Guest/ResetPassword.vue";
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

describe("@/Pages/Guest/ResetPassword.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка формы регистрации", () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(ResetPassword, {
            props: {
                email: 'test@example.com',
                token: 'testtoken',
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ResetPassword'
                    }
                },
                provide: { filmsCatalog }
            }
        });
        
        expect(wrapper.vm.form.token).toBe('testtoken');
        expect(wrapper.vm.form.email).toBe('test@example.com');
        expect(wrapper.vm.form.password).toBe('');
        expect(wrapper.vm.form.password_confirmation).toBe('');
        expect(wrapper.vm.form.errors).toStrictEqual({});
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
        
        const formTag = wrapper.get('form');
        expect(formTag.isVisible()).toBe(true);
        
        const label = formTag.findAll('label');
        expect(label.length).toBe(3);
        expect(label[0].text()).toBe('Электронная почта:');
        expect(label[1].text()).toBe('Пароль:');
        expect(label[2].text()).toBe('Подтверждение пароля:');
        
        const input = formTag.findAll('input');
        expect(input.length).toBe(3);
        expect(input[0].element.value).toBe('test@example.com');
        expect(input[1].element.value).toBe('');
        expect(input[2].element.value).toBe('');
        
        input[1].setValue('TestPassword');
        expect(input[1].element.value).toBe('TestPassword');
        expect(wrapper.vm.form.password).toBe('TestPassword');
        
        input[2].setValue('TestPassword');
        expect(input[2].element.value).toBe('TestPassword');
        expect(wrapper.vm.form.password_confirmation).toBe('TestPassword');
        
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
        expect(button.find('span').text()).toBe('Задать новый пароль');
        expect(button.findComponent(Spinner).exists()).toBe(false);
    });
    
    it("Отправка формы регистрации", async () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(ResetPassword, {
            props: {
                email: 'test@example.com',
                token: 'testtoken',
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/ResetPassword'
                    }
                },
                provide: { filmsCatalog }
            }
        });
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        const formTag = wrapper.get('form');
        const formButton = formTag.getComponent(FormButton);
        const button = formButton.get('button');
        
        expect(formPost).not.toHaveBeenCalled();
        await button.trigger('submit');
        expect(formPost).toHaveBeenCalledTimes(1);
    });
});

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import AuthContentTabs from '@/components/Tabs/AuthContentTabs.vue';
import * as mod from '@/Services/inertia';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';
import { app } from '@/Services/app';

vi.spyOn(mod, 'useGlobalRequest');

const getWrapper = function(pageComponent, is_admin = false) {
    return mount(AuthLayout, {
            props: {
                errors: null,
                user: {
                    is_admin
                }
            },
            global: {
                stubs: {
                    AuthContentTabs: true
                },
                mocks: {
                    $page: {
                        component: pageComponent
                    }
                },
                provide: {
                    filmsList: useFilmsListStore(),
                    filmsAccount: useFilmsAccountStore()
                }
            }
        });
};

const forbiddenModalExists = function(wrapper) {
    const forbiddenModal = wrapper.findComponent(ForbiddenModal);
    expect(forbiddenModal.exists()).toBe(true);
    expect(forbiddenModal.html()).toBe('<!--v-if-->');
};

describe("@/Layouts/AuthLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
    });
    
    it("Монтирование шаблона AuthLayout для не админа", () => {
        const wrapper = getWrapper('Auth/Films');
        
        // Присутствует компонента AuthContentTabs
        expect(wrapper.getComponent(AuthContentTabs).isVisible()).toBe(true);

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 3 ссылки (компонента AuthContentTabs отключена) 
        const li = nav.findAll('li');
        expect(li.length).toBe(3);
        
        // Первая вкладка - неактивная ссылка
        const a0 = li[0].get('a');
        expect(a0.attributes('href')).toBe('/');
        expect(a0.classes('router-link-active')).toBe(false);
        // Содержит иконку HouseSvg
        expect(a0.findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'каталог' не активна с дефолтным url
        const a1 = li[1].get('a');
        expect(a1.attributes('href')).toBe(wrapper.vm.filmsAccount.getUrl('/userfilms'));
        expect(a1.classes('router-link-active')).toBe(false);
        expect(a1.text()).toBe('лк');
        
        // Четвёртая ссылка 'выход' не активна
        const button = li[2].get('button');
        expect(button.text()).toBe('выход');
        
        // Присутствует пустая компонента ForbiddenModal
        forbiddenModalExists(wrapper);
        
        // Отсутствует ссылка на страницу админа
        expect(nav.find('a[href="/admin"]').exists()).toBe(false);
    });
    
    it("Монтирование шаблона AuthLayout для админа", () => {
        const wrapper = getWrapper('Auth/Account/UserFilms', true);
        
        // Присутствует компонента AuthContentTabs
        expect(wrapper.getComponent(AuthContentTabs).isVisible()).toBe(true);

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 4 ссылки (компонента AuthContentTabs отключена)
        const li = nav.findAll('li');
        expect(li.length).toBe(4);
        
        // Первая ссылка не активна
        const a0 = li[0].get('a');
        expect(a0.attributes('href')).toBe('/');
        expect(a0.classes('router-link-active')).toBe(false);
        // Содержит иконку HouseSvg
        expect(a0.findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'лк' активна с дефолтным url ($page.component === 'Auth/Account/UserFilms')
        const a1 = li[1].get('a');
        expect(a1.attributes('href')).toBe(wrapper.vm.filmsAccount.getUrl('/userfilms'));
        expect(a1.classes('router-link-active')).toBe(true);
        expect(a1.text()).toBe('лк');

        // Третья ссылка 'администрирование' не активна
        const a2 = li[2].get('a');
        expect(a2.attributes('href')).toBe('/admin');
        expect(a2.classes('router-link-active')).toBe(false);
        expect(a2.text()).toBe('администрирование');
        
        // Четвёртая ссылка 'выход' не активна
        const button = li[3].get('button');
        expect(button.text()).toBe('выход');
        
        // Присутствует пустая компонента ForbiddenModal
        forbiddenModalExists(wrapper);
    });
    
    it("Видна компонента GlobalModal", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        const wrapper = getWrapper('Auth/Films');

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(true);
    });
    
    it("Компонента GlobalModal не видна, т.к. app.isRequest = true", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        app.isRequest = true;
        const wrapper = getWrapper('Auth/Account/UserFilms', true);

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(false);
    });
});

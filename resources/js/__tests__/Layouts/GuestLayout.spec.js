import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore } from '@/Stores/films';

describe("@/Layouts/GuestLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона GuestLayout", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = mount(GuestLayout, {
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Home'
                    }
                },
                provide: { app, filmsList }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 3 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(3);
        
        // Первая вкладка - активная ссылка ($page.component === 'Guest/Home')
        const a0 = li[0].find('a');
        expect(a0.attributes('href')).toBe('/guest');
        expect(a0.attributes('class')).toContain('router-link-active');
        // Содержит иконку HouseSvg
        expect(a0.findComponent(HouseSvg).exists()).toBe(true);

        // Вторая вкладка - выпадашка
        const span = li[1].find('span');
        expect(span.attributes('class')).not.toContain('router-link-active');
        expect(span.text()).toBe('контент');
        // Ссылки выпадашки отсутствуют
        expect(li[1].find('ul').exists()).toBe(false);
        
        // Третья вкладка - неактивная ссылка 'вход'
        const a2 = li[2].find('a');
        expect(a2.attributes('href')).toBe('/login');
        expect(a2.attributes('class')).not.toContain('router-link-active');
        expect(a2.text()).toBe('вход');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
    
    it("Проверка выпадашки", async () => {
        const app = useAppStore();
        
        const filmsList = useFilmsListStore();
        filmsList.page = 5;
        filmsList.perPage = 100;
        filmsList.title = 'abc';
        filmsList.description = 'xy';
        
        const wrapper = mount(GuestLayout, {
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Films'
                    }
                },
                provide: { app, filmsList }
            }
        });

        const nav = wrapper.find('nav');
        const liNav = nav.findAll('li');
        
        // Вкладка 'контент'
        const span = liNav[1].find('span');
        expect(span.text()).toBe('контент');
        // Ссылки выпадашки отсутствуют
        expect(liNav[1].find('ul').exists()).toBe(false);
        
        // После клика по выпадашке появляются ссылки
        await span.trigger('click');
        expect(liNav[1].find('ul').exists()).toBe(true);
        const liUl = liNav[1].find('ul').findAll('li');
        expect(liUl.length).toBe(2);
        
        // Первая вкладка - активная ссылка 'фильмы'
        const a0 = liUl[0].find('a');
        expect(a0.exists()).toBe(true);
        expect(a0.attributes('href')).toBe('/guest/films?page=5&number=100&title=abc&description=xy');
        expect(a0.attributes('class')).toContain('tabs-link-active');
        expect(a0.text()).toBe('фильмы');
        
        // Вторая вкладка - неактивная ссылка 'города'
        const a1 = liUl[1].find('a');
        expect(a1.exists()).toBe(true);
        expect(a1.attributes('href')).toBe('/guest/cities');
        expect(a1.attributes('class')).not.toContain('tabs-link-active');
        expect(a1.text()).toBe('города');
        
        // Повторный клик убирает ссылки
        await span.trigger('click');
        expect(liNav[1].find('ul').exists()).toBe(false);
    });
});

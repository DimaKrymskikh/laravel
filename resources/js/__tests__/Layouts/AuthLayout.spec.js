import { mount, flushPromises } from "@vue/test-utils";

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

describe("@/Layouts/AuthLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AuthLayout для не админа", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = mount(AuthLayout, {
            props: {
                errors: null,
                user: {
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 4 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(4);
        
        // Первая вкладка - неактивная ссылка
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(false);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/"]').findComponent(HouseSvg).exists()).toBe(true);

        // Вторая вкладка 'контент' активная
        expect(li[1].find('.router-link-active').exists()).toBe(true);
        expect(li[1].find('span').text()).toBe('контент');
        // Ссылки выпадашки отсутствуют
        expect(li[1].find('ul').exists()).toBe(false);

        // Третья ссылка 'каталог' не активна с дефолтным url
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[2].find('.router-link-active').exists()).toBe(false);
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').text()).toBe('лк');
        
        // Четвёртая ссылка 'выход' не активна
        expect(li[3].find('a[href="/logout"]').exists()).toBe(true);
        expect(li[3].find('.router-link-active').exists()).toBe(false);
        expect(li[3].find('a[href="/logout"]').text()).toBe('выход');
        
        // Присутствует пустая компонента ForbiddenModal
        forbiddenModalExists(wrapper);
        
        // Отсутствует ссылка на страницу админа
        expect(nav.find('a[href="/admin"]').exists()).toBe(false);
    });
    
    it("Монтирование шаблона AuthLayout для админа", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = mount(AuthLayout, {
            props: {
                errors: null,
                user: {
                    is_admin: true
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Account'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 5 ссылок
        const li = nav.findAll('li');
        expect(li.length).toBe(5);
        
        // Первая ссылка не активна
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(false);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/"]').findComponent(HouseSvg).exists()).toBe(true);
        
        // Вторая вкладка 'контент' не активная
        expect(li[1].find('.router-link-active').exists()).toBe(false);
        expect(li[1].find('span').text()).toBe('контент');
        // Ссылки выпадашки отсутствуют
        expect(li[1].find('ul').exists()).toBe(false);

        // Третья ссылка 'лк' активна с дефолтным url ($page.component === 'Auth/Account')
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[2].find('.router-link-active').exists()).toBe(true);
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').text()).toBe('лк');

        // Третья ссылка 'администрирование' не активна
        expect(li[3].find('a[href="/admin"]').exists()).toBe(true);
        expect(li[3].find('.router-link-active').exists()).toBe(false);
        expect(li[3].find('a[href="/admin"]').text()).toBe('администрирование');
        
        // Пятая ссылка 'выход' не активна
        expect(li[4].find('a[href="/logout"]').exists()).toBe(true);
        expect(li[4].find('.router-link-active').exists()).toBe(false);
        expect(li[4].find('a[href="/logout"]').text()).toBe('выход');
        
        // Присутствует пустая компонента ForbiddenModal
        forbiddenModalExists(wrapper);
    });
    
    it("Проверка выпадашки", async () => {
        const app = useAppStore();
        
        const filmsList = useFilmsListStore();
        filmsList.page = 5;
        filmsList.perPage = 100;
        filmsList.title = 'abc';
        filmsList.description = 'xy';
        
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(AuthLayout, {
            props: {
                errors: null,
                user: {
                    is_admin: true
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
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
        expect(liUl.length).toBe(3);
        
        // Первая вкладка - активная ссылка 'фильмы'
        expect(liUl[0].find('a[href="/films?page=5&number=100&title=abc&description=xy"]').exists()).toBe(true);
        expect(liUl[0].find('.tabs-link-active').exists()).toBe(true);
        expect(liUl[0].find('a[href="/films?page=5&number=100&title=abc&description=xy"]').text()).toBe('фильмы');
        
        // Вторая вкладка - неактивная ссылка 'города'
        expect(liUl[1].find('a[href="/cities"]').exists()).toBe(true);
        expect(liUl[1].find('.tabs-link-active').exists()).toBe(false);
        expect(liUl[1].find('a[href="/cities"]').text()).toBe('города');
        
        // Повторный клик убирает ссылки
        await span.trigger('click');
        expect(liNav[1].find('ul').exists()).toBe(false);
    });
    
    const forbiddenModalExists = function(wrapper) {
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    };
});

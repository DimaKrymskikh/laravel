import { mount, flushPromises } from "@vue/test-utils";

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { filmsCatalogStore, filmsAccountStore } from '@/Stores/films';

describe("@/Layouts/AuthLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AuthLayout для не админа", () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
     
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
                        component: 'Auth/Catalog'
                    }
                },
                provide: { filmsCatalog, filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 4 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(4);
        
        // Первая ссылка не активна
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(false);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/"]').findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'каталог' активна с дефолтным url ($page.component === 'Auth/Catalog')
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[1].find('.router-link-active').exists()).toBe(true);
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').text()).toBe('каталог');

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
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
     
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
                provide: { filmsCatalog, filmsAccount }
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

        // Вторая ссылка 'каталог' не активна с дефолтным url ($page.component === 'Auth/Catalog')
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[1].find('.router-link-active').exists()).toBe(false);
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').text()).toBe('каталог');

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
    
    const forbiddenModalExists = function(wrapper) {
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    };
});

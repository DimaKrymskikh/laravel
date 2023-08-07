import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { filmsCatalogStore } from '@/Stores/films';

describe("@/Layouts/GuestLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона GuestLayout", () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(GuestLayout, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Home'
                    }
                },
                provide: { filmsCatalog }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 3 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(3);
        
        // Первая ссылка активна ($page.component === 'Guest/Home')
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(true);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/"]').findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'каталог' не активна с дефолтным url
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[1].find('.router-link-active').exists()).toBe(false);
        expect(li[1].find('a[href="/catalog?page=1&number=20&title=&description="]').text()).toBe('каталог');
        
        // Третья ссылка 'вход' не активна
        expect(li[2].find('a[href="/login"]').exists()).toBe(true);
        expect(li[2].find('.router-link-active').exists()).toBe(false);
        expect(li[2].find('a[href="/login"]').text()).toBe('вход');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
    
    it("Проверка ссылки на страницу 'каталог'", () => {
        const filmsCatalog = filmsCatalogStore();
        filmsCatalog.page = 5;
        filmsCatalog.perPage = 100;
        filmsCatalog.title = 'abc';
        filmsCatalog.description = 'xy';
        
        const wrapper = mount(GuestLayout, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Catalog'
                    }
                },
                provide: { filmsCatalog }
            }
        });

        const nav = wrapper.find('nav');
        const li = nav.findAll('li');

        // Вторая ссылка 'каталог' активна с правильным url
        expect(li[1].find('a[href="/catalog?page=5&number=100&title=abc&description=xy"]').exists()).toBe(true);
        expect(li[1].find('.router-link-active').exists()).toBe(true);
    });
    
    it("Отображение модального окна для ошибки", async () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(GuestLayout, {
            props: {
                errors: {
                    message: 'Некоторая ошибка'
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Catalog'
                    }
                },
                provide: { filmsCatalog }
            }
        });

        // Модальное окно компоненты ForbiddenModal присутствует
        const forbiddenModal = wrapper.get('#forbidden-modal');
        expect(forbiddenModal.isVisible()).toBe(true);
        
        // Отображается сообщение об ошибке
        const errorsMessage = forbiddenModal.get('#errors-message');
        expect(errorsMessage.text()).toBe('Некоторая ошибка');
    });
});

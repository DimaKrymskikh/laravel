import { mount, flushPromises } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

describe("@/Layouts/AdminLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AdminLayout", () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = mount(AdminLayout, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Home'
                    }
                },
                provide: { app, filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 3 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(3);
        
        // Первая ссылка активна ($page.component === 'Admin/Home')
        expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(true);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/admin"]').findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'города' не активна
        expect(li[1].find('.router-link-active').exists()).toBe(false);
        expect(li[1].find('span').text()).toBe('контент');
        // Ссылки выпадашки отсутствуют
        expect(li[1].find('ul').exists()).toBe(false);

        // Третья ссылка 'лк' не активна с дефолтным url
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[2].find('.router-link-active').exists()).toBe(false);
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').text()).toBe('лк');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
    
    it("Проверка выпадашки", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = mount(AdminLayout, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Cities'
                    }
                },
                provide: { app, filmsAccount }
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
        expect(liUl.length).toBe(1);
        
        // Первая вкладка - активная ссылка ссылка 'города'
        expect(liUl[0].find('a[href="/admin/cities"]').exists()).toBe(true);
        expect(liUl[0].find('.tabs-link-active').exists()).toBe(true);
        expect(liUl[0].find('a[href="/admin/cities"]').text()).toBe('города');
        
        // Повторный клик убирает ссылки
        await span.trigger('click');
        expect(liNav[1].find('ul').exists()).toBe(false);
    });
});

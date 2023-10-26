import { mount } from "@vue/test-utils";

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AccountRemoveBlock from '@/Components/Pages/Auth/Account/AccountRemoveBlock.vue';
import AdminBlock from '@/Components/Pages/Auth/Account/AdminBlock.vue';
import PersonalDataBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock.vue';
import AuthAccountTabs from '@/Components/Tabs/AuthAccountTabs.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

describe("@/Layouts/Auth/AccountLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AccountLayout", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const user = {
                id: 77,
                is_admin: false,
                login: 'TestLogin'
            };
                
        const linksList = [{
                link: '/',
                text: 'Главная страница'
            }, {
                text: 'ЛК'
            }];
     
        const wrapper = mount(AccountLayout, {
            props: {
                errors: null,
                user,
                linksList
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Account/UserFilms'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        // Отрисовывается заголовок
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Добрый день, TestLogin');
        // Проверяем наличие компонент
        expect(wrapper.getComponent(BreadCrumb).props('linksList')).toStrictEqual(linksList);
        expect(wrapper.getComponent(AccountRemoveBlock).isVisible()).toBe(true);
        expect(wrapper.getComponent(AdminBlock).props('user')).toStrictEqual(user);
        expect(wrapper.getComponent(PersonalDataBlock).props('user')).toStrictEqual(user);
        expect(wrapper.getComponent(AuthAccountTabs).isVisible()).toBe(true);
    });
});

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PersonalDataBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock.vue';
import AuthAccountTabs from '@/Components/Tabs/AuthAccountTabs.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

import { userAuth } from '@/__tests__/data/users';
                
const linksList = [{
        link: '/',
        text: 'Главная страница'
    }, {
        text: 'ЛК'
    }];

const getWrapper = function(app, filmsList, filmsAccount) {
    return mount(AccountLayout, {
            props: {
                errors: null,
                user: userAuth,
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
};

describe("@/Layouts/Auth/AccountLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AccountLayout", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = getWrapper(app, filmsList, filmsAccount);
        
        // Отрисовывается заголовок
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe(`Добрый день, ${userAuth.login}`);
        // Проверяем наличие компонент
        expect(wrapper.getComponent(BreadCrumb).props('linksList')).toStrictEqual(linksList);
        expect(wrapper.getComponent(PersonalDataBlock).props('user')).toStrictEqual(userAuth);
        expect(wrapper.getComponent(AuthAccountTabs).isVisible()).toBe(true);
    });
});

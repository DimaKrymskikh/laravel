import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UserWeather from "@/Pages/Auth/Account/UserWeather.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';

import {  cities_with_weather } from '@/__tests__/data/cities';
import { AccountLayoutStub } from '@/__tests__/methods/stubs';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});
        
const user = {
            id: 77,
            is_admin: false,
            login: 'TestLogin'
        };

const getWrapper = function() {
    return mount(UserWeather, {
            props: {
                errors: null,
                cities:  cities_with_weather,
                user
            },
            global: {
                stubs: {
                    AccountLayout: AccountLayoutStub
                },
                mocks: {
                    $page: {
                        component: 'Auth/Account/UserWeather'
                    }
                }
            }
        });
};

describe("@/Pages/Auth/Account/UserWeather.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка UserWeather", () => {
        const wrapper = getWrapper();
        
        const accountLayout = wrapper.getComponent(AccountLayout);
        expect(accountLayout.props('user')).toStrictEqual(user);
        expect(accountLayout.props('errors')).toStrictEqual(null);
        expect(accountLayout.props('linksList')).toStrictEqual(wrapper.vm.linksList);
        
        const flexes = wrapper.findAll('div.flex.justify-between.border-b');
        expect(flexes.length).toBe(4);
        
        const h3s = flexes[0].findAll('h3');
        expect(h3s.length).toBe(2);
        expect(h3s[0].text()).toBe('Город');
        expect(h3s[1].text()).toBe('Последние данные о погоде');
        
        expect(flexes[1].text()).toContain(wrapper.vm.cities[0].name);
        expect(flexes[1].text()).toContain('(часовой пояс не указан)');
        expect(flexes[1].text()).not.toContain('Для города ещё не получены данные о погоде');
        
        expect(flexes[2].text()).toContain(wrapper.vm.cities[1].name);
        expect(flexes[2].text()).not.toContain('(часовой пояс не указан)');
        expect(flexes[2].text()).not.toContain('Для города ещё не получены данные о погоде');
        
        expect(flexes[3].text()).toContain(wrapper.vm.cities[2].name);
        expect(flexes[3].text()).toContain('Для города ещё не получены данные о погоде');
    });
});

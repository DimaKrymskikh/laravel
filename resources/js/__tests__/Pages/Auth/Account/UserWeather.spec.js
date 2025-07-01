import '@/bootstrap';
import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import UserWeather from "@/Pages/Auth/Account/UserWeather.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import RemoveCityFromListOfWeatherModal from '@/Components/Pages/Auth/Account/UserWeather/RemoveCityFromListOfWeatherModal.vue';
import ArrowPathSvg from '@/Components/Svg/ArrowPathSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import { useWeatherPageAuthStore } from '@/Stores/weather';

import {  cities_with_weather } from '@/__tests__/data/cities';
import { weatherForOneCity } from '@/__tests__/data/weather';
import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';

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
                    AccountLayout: AuthAccountLayoutStub,
                    RemoveCityFromListOfWeatherModal: true
                },
                provide: {
                    weatherPageAuth: useWeatherPageAuthStore()
                }
            }
        });
};

describe("@/Pages/Auth/Account/UserWeather.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
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
        
        expect(wrapper.findComponent(RemoveCityFromListOfWeatherModal).exists()).toBe(false);
        
        const echoAuth = wrapper.getComponent(EchoAuth);
        expect(echoAuth.props('user')).toStrictEqual(user);
        expect(echoAuth.props('action')).toStrictEqual(wrapper.vm.refreshCityWeather);
        expect(echoAuth.props('events')).toStrictEqual(['RemoveCityFromWeatherList', 'RefreshCityWeather']);
    });
    
    it("Клик по TrashSvg показывает модальное окно для удаления фильма из просмотра погоды", async () => {
        const wrapper = getWrapper();
        
        const flexes = wrapper.findAll('div.flex.justify-between.border-b');
        expect(flexes.length).toBe(4);
        
        const trashSvg = flexes[2].getComponent(TrashSvg);
        expect(trashSvg.props('title')).toBe('Удалить город из списка просмотра погоды');
        // Модальное окно скрыто
        expect(wrapper.findComponent(RemoveCityFromListOfWeatherModal).exists()).toBe(false);
        await trashSvg.trigger('click');
        // Появилось модальное окно
        expect(wrapper.findComponent(RemoveCityFromListOfWeatherModal).exists()).toBe(true);
    });
    
    it("Функция hideRemoveCityFromListOfWeatherModal изменяет isShowRemoveCityFromListOfWeatherModal с true на false", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.isShowRemoveCityFromListOfWeatherModal = true;
        wrapper.vm.hideRemoveCityFromListOfWeatherModal();
        expect(wrapper.vm.isShowRemoveCityFromListOfWeatherModal).toBe(false);
    });
    
    it("Клик по ArrowPathSvg отправляет запрос на сервер", async () => {
        const appRequest = vi.spyOn(app, 'request');
        
        const wrapper = getWrapper();
        
        const flexes = wrapper.findAll('div.flex.justify-between.border-b');
        expect(flexes.length).toBe(4);
        
        const arrowPathSvg = flexes[2].getComponent(ArrowPathSvg);
        expect(arrowPathSvg.props('title')).toBe('Получить последние данные погоды в городе');
        
        expect(appRequest).not.toHaveBeenCalled();
        await arrowPathSvg.trigger('click');
        expect(appRequest).toHaveBeenCalledTimes(1);
    });
    
    it("refreshCityWeather", async () => {
        const wrapper = getWrapper();
        
        wrapper.vm.cities.forEach( city => {
            expect(city.weather).not.toStrictEqual(weatherForOneCity);
        });
        wrapper.vm.refreshCityWeather(weatherForOneCity);
        // Для первого города погода изменилась
        expect(wrapper.vm.cities[0].weather).toStrictEqual(weatherForOneCity);
        // Для остальных городов осталась прежней
        for(let i = 1; i < wrapper.vm.cities.length; i++) {
            expect(wrapper.vm.cities[i].weather).not.toStrictEqual(weatherForOneCity);
        }
    });
});

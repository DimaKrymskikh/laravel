import '@/bootstrap';
import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UserLogsWeather from "@/Pages/Auth/Account/UserLogsWeather.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { useAppStore } from '@/Stores/app';
import { useWeatherPageAuthStore } from '@/Stores/weather';

import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';
import { city } from '@/__tests__/data/cities';
import { userAuth } from '@/__tests__/data/users';
import { weather3 } from '@/__tests__/data/weather';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            get: vi.fn()
        },
        Head: vi.fn()
    };
});

const getWrapper = function(app, weatherPageAuth) {
    return mount(UserLogsWeather, {
            props: {
                errors: {},
                weatherPage: weather3,
                city,
                user: userAuth
            },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub
                },
                provide: { app, weatherPageAuth }
            }
        });
};

describe("@/Pages/Auth/Account/UserLogsWeather.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка UserLogsWeather", () => {
        const app = useAppStore();
        const weatherPageAuth = useWeatherPageAuthStore();
        
        const wrapper = getWrapper(app, weatherPageAuth);
        
        const accountLayout = wrapper.getComponent(AccountLayout);
        expect(accountLayout.props('user')).toStrictEqual(userAuth);
        expect(accountLayout.props('errors')).toStrictEqual({});
        expect(accountLayout.props('linksList')).toStrictEqual(wrapper.vm.linksList);
        
        const h2 = wrapper.get('h2');
        expect(h2.text()).toBe('История погоды в городе ' + city.name);
        
        // Отрисовывается таблица с данными погоды
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Отрисовывается заголовок к таблице
        const caption = table.get('caption');
        expect(caption.text()).toBe(`Показано ${weather3.per_page} показаний погоды с ${weather3.from} по ${weather3.to} из ${weather3.total}`);
        
        const thead = table.get('thead');
        const ths = thead.findAll('th');
        expect(ths.length).toBe(9);
        expect(ths[0].text()).toBe('#');
        expect(ths[1].text()).toBe('Время и дата');
        expect(ths[2].text()).toBe('Описание');
        expect(ths[3].text()).toBe('Температура');
        expect(ths[4].text()).toBe('Атм. давление');
        expect(ths[5].text()).toBe('Влажность');
        expect(ths[6].text()).toBe('Видимость');
        expect(ths[7].text()).toBe('Ветер');
        expect(ths[8].text()).toBe('Облачность');
        
        const tbody = table.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(3);
        
        const tds = trs[1].findAll('td');
        expect(tds.length).toBe(9);
        expect(tds[0].text()).toBe(`${weather3.from + 1}`);
        expect(tds[1].text()).toBe(weather3.data[1].created_at);
        expect(tds[2].text()).toBe(weather3.data[1].weather_description);
        expect(tds[3].text()).toBe(weather3.data[1].main_temp + ' C\u00B0 ' + '( ощущается как ' + weather3.data[1].main_feels_like + ' C\u00B0 )');
        expect(tds[4].text()).toBe(weather3.data[1].main_pressure + ' hPa');
        expect(tds[5].text()).toBe(weather3.data[1].main_humidity + '%');
        expect(tds[6].text()).toBe(weather3.data[1].visibility + ' метров');
        expect(tds[7].text()).toBe('скорость ветра ' + weather3.data[1].wind_speed + ' м/c  направление ветра ' + weather3.data[1].wind_deg + '\u00B0');
        expect(tds[8].text()).toBe(weather3.data[1].clouds_all + '%');
    });
    
    it("Функция changeNumberOfWeatherOnPage отправляет запрос на изменение числа записей погоды на странице", () => {
        const app = useAppStore();
        const weatherPageAuth = useWeatherPageAuthStore();
        
        const wrapper = getWrapper(app, weatherPageAuth);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        wrapper.vm.changeNumberOfWeatherOnPage(50);
        // Функция changeNumberOfFilmsOnPage отправила запрос с нужными параметрами
        expect(wrapper.vm.weatherPageAuth.page).toBe(1);
        expect(wrapper.vm.weatherPageAuth.perPage).toBe(50);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.weatherPageAuth.getUrl(`/userlogsweather/${city.id}`));
    });
});

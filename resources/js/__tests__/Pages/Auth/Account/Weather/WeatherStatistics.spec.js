import '@/bootstrap';
import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import WeatherStatistics from "@/Pages/Auth/Account/Weather/WeatherStatistics.vue";
import { messageEmptyTable } from '@/Services/Content/Weather/weatherStatistics';

import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';
import { city } from '@/__tests__/data/cities';
import { userAuth } from '@/__tests__/data/users';
import { weatherWeekStatistics } from '@/__tests__/data/weather/weatherStatistics';

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

const getWrapper = function(weather = []) {
    return mount(WeatherStatistics, {
            props: {
                errors: {},
                weatherPage: weather,
                city,
                user: userAuth
            },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub
                }
            }
        });
};

describe("@/Pages/Auth/Account/Weather/WeatherStatistics.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Отрисовка WeatherStatistics со статистикой", () => {
        const wrapper = getWrapper(weatherWeekStatistics);
        
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        const tbody = wrapper.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(weatherWeekStatistics.length);
        
        expect(wrapper.text()).not.toContain(messageEmptyTable);
    });
    
    it("Отрисовка WeatherStatistics без статистики", () => {
        const wrapper = getWrapper();
        
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        const tbody = wrapper.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(0);
        
        expect(wrapper.text()).toContain(messageEmptyTable);
    });
});

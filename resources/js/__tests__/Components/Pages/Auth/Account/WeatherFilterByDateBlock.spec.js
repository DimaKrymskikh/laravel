import '@/bootstrap';
import { mount, flushPromises } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import WeatherFilterByDateBlock from '@/Components/Pages/Auth/Account/WeatherFilterByDateBlock.vue';
import ImputPikaday from '@/Components/Elements/ImputPikaday.vue';
import BackSpaceSvg from '@/Components/Svg/BackSpaceSvg.vue';
import { useWeatherPageAuthStore } from '@/Stores/weather';
        
const getWrapper = function(weatherPageAuth, refreshWeather) {
    return mount(WeatherFilterByDateBlock, {
            props: {
                refreshWeather
            },
            global: {
                provide: { weatherPageAuth }
            }
        });
};

describe("@/Components/Pages/Auth/Account/WeatherFilterByDateBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка WeatherFilterByDateBlock", async () => {
        const weatherPageAuth = useWeatherPageAuthStore();
        const refreshWeather = vi.fn();
        
        const wrapper = getWrapper(weatherPageAuth, refreshWeather);
        
        // Имеются два input-поля для дат
        const imputPikadays = wrapper.findAllComponents(ImputPikaday);
        expect(imputPikadays.length).toBe(2);
        expect(imputPikadays[0].props('datepicker')).toBe('from');
        expect(imputPikadays[1].props('datepicker')).toBe('to');
        
        // Задаём значение в первом input-поле для дат 
        const input0 = imputPikadays[0].get('input');
        input0.setValue('01.01.2024');
        // Изменяется дата
        expect(imputPikadays[0].emitted()).toHaveProperty('update:modelValue');
        await flushPromises();
        expect(wrapper.vm.datefrom).toBe('01.01.2024');
        expect(weatherPageAuth.datefrom).toBe('01.01.2024');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(1);
        
        // Задаём значение во втором input-поле для дат 
        const input1 = imputPikadays[1].get('input');
        input1.setValue('30.04.2024');
        // Изменяется дата
        expect(imputPikadays[1].emitted()).toHaveProperty('update:modelValue');
        await flushPromises();
        expect(wrapper.vm.dateto).toBe('30.04.2024');
        expect(weatherPageAuth.dateto).toBe('30.04.2024');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(2);
        
        // Имеются две иконки для очиски дат
        const backSpaceSvg = wrapper.findAllComponents(BackSpaceSvg);
        expect(backSpaceSvg.length).toBe(2);
        expect(backSpaceSvg[0].props('title')).toBe('Очистить дату');
        expect(backSpaceSvg[1].props('title')).toBe('Очистить дату');
        
        // Кликаем по первой иконке
        await backSpaceSvg[0].trigger('click');
        // Дата очищается
        await flushPromises();
        expect(wrapper.vm.datefrom).toBe('');
        expect(weatherPageAuth.datefrom).toBe('');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(3);
        
        // Кликаем по второй иконке
        await backSpaceSvg[1].trigger('click');
        // Дата очищается
        await flushPromises();
        expect(wrapper.vm.dateto).toBe('');
        expect(weatherPageAuth.dateto).toBe('');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(4);
    });
    
    it("Задание свойств weatherPageAuth при обновлении страницы (window.location.search = null)", async () => {
        const weatherPageAuth = useWeatherPageAuthStore();
        const refreshWeather = vi.fn();
        
        window.location.search = null;
        
        const wrapper = getWrapper(weatherPageAuth, refreshWeather);
        expect(weatherPageAuth.datefrom).toBe('');
        expect(weatherPageAuth.dateto).toBe('');
    });
    
    it("Задание свойств weatherPageAuth при обновлении страницы (window.location.search not null)", async () => {
        const weatherPageAuth = useWeatherPageAuthStore();
        const refreshWeather = vi.fn();
        
        window.location.search = '?datefrom=01.01.2024&dateto=31.01.20024';
        
        const wrapper = getWrapper(weatherPageAuth, refreshWeather);
        expect(weatherPageAuth.datefrom).toBe('01.01.2024');
        expect(weatherPageAuth.dateto).toBe('31.01.20024');
    });
});

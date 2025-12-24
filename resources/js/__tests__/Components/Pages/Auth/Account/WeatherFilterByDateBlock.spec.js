import '@/bootstrap';
import { mount, flushPromises } from "@vue/test-utils";

import { paginationOptionsForWeatherLogs } from "@/Services/Content/Weather/weatherLogs.js";
import WeatherFilterByDateBlock from '@/Components/Pages/Auth/Account/WeatherFilterByDateBlock.vue';
import InputPikaday from '@/Components/Elements/InputPikaday.vue';
import BackSpaceSvg from '@/Components/Svg/BackSpaceSvg.vue';
        
const getWrapper = function(paginationOptionsForWeatherLogs, refreshWeather) {
    return mount(WeatherFilterByDateBlock, {
            props: {
                refreshWeather
            },
            global: {
                provide: { paginationOptionsForWeatherLogs }
            }
        });
};

describe("@/Components/Pages/Auth/Account/WeatherFilterByDateBlock.vue", () => {
    it("Отрисовка WeatherFilterByDateBlock", async () => {
        const refreshWeather = vi.fn();
        
        const wrapper = getWrapper(paginationOptionsForWeatherLogs, refreshWeather);
        
        // Имеются два input-поля для дат
        const InputPikadays = wrapper.findAllComponents(InputPikaday);
        expect(InputPikadays.length).toBe(2);
        expect(InputPikadays[0].props('datepicker')).toBe('from');
        expect(InputPikadays[1].props('datepicker')).toBe('to');
        
        // Задаём значение в первом input-поле для дат 
        const input0 = InputPikadays[0].get('input');
        input0.setValue('01.01.2024');
        // Изменяется дата
        expect(InputPikadays[0].emitted()).toHaveProperty('update:modelValue');
        await flushPromises();
        expect(wrapper.vm.datefrom).toBe('01.01.2024');
        expect(paginationOptionsForWeatherLogs.datefrom).toBe('01.01.2024');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(1);
        
        // Задаём значение во втором input-поле для дат 
        const input1 = InputPikadays[1].get('input');
        input1.setValue('30.04.2024');
        // Изменяется дата
        expect(InputPikadays[1].emitted()).toHaveProperty('update:modelValue');
        await flushPromises();
        expect(wrapper.vm.dateto).toBe('30.04.2024');
        expect(paginationOptionsForWeatherLogs.dateto).toBe('30.04.2024');
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
        expect(paginationOptionsForWeatherLogs.datefrom).toBe('');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(3);
        
        // Кликаем по второй иконке
        await backSpaceSvg[1].trigger('click');
        // Дата очищается
        await flushPromises();
        expect(wrapper.vm.dateto).toBe('');
        expect(paginationOptionsForWeatherLogs.dateto).toBe('');
        // Отправляется запрос на сервер
        expect(refreshWeather).toHaveBeenCalledTimes(4);
    });
});

import { mount } from "@vue/test-utils";

import Dropdown from "@/components/Elements/Dropdown.vue";

describe("@/components/Elements/Dropdown.vue", () => {
    it("Монтирование компоненты, выпадение/сокрытие списка", async () => {
        const wrapper = mount(Dropdown, {
            props: {
                buttonName: 'Текст кнопки',
                itemsNumberOnPage: 50,
                changeNumber: vi.fn()
            }
        });

        const button = wrapper.get('button');
        expect(button.text()).toBe('Текст кнопки');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке
        await button.trigger('click');
        // Появился список из 4 вариантов
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(4);
        // Первый вариант не выбран
        expect(li[0].text()).toBe('10');
        expect(li[0].attributes('data-number')).toBe('10');
        expect(li[0].classes('select')).toBe(false);
        // Второй вариант не выбран
        expect(li[1].text()).toBe('20');
        expect(li[1].attributes('data-number')).toBe('20');
        expect(li[1].classes('select')).toBe(false);
        // Третий вариант выбран
        expect(li[2].text()).toBe('50');
        expect(li[2].attributes('data-number')).toBe('50');
        expect(li[2].classes('select')).toBe(true);
        // Четвёртый вариант не выбран
        expect(li[3].text()).toBe('100');
        expect(li[3].attributes('data-number')).toBe('100');
        expect(li[3].classes('select')).toBe(false);
        
        // Второй раз кликаем по кнопке
        await button.trigger('click');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
    });
    
    it("Изменение выбора", async () => {
        // Для отслеживания параметра запроса
        const changeNumber = vi.fn((n) => n);
        
        const wrapper = mount(Dropdown, {
            props: {
                buttonName: 'Текст кнопки',
                itemsNumberOnPage: 50,
                changeNumber
            }
        });

        const button = wrapper.get('button');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        // Кликаем по кнопке
        await button.trigger('click');
        // Появился список из 4 вариантов
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(4);
/**
* При тестировании делегированного события нужно кликать по дочерним элементам
* Когда событие удаляет элементы, к узлам DOM нужно обращаться через wrapper.find 
* (использование сохранённых узлов до события, например, ul приводит к ошибкам)
*/
        // Кликаем по активному выбору (50)
        await li[2].trigger('click');
        // Список, по-прежнему, виден
        expect(wrapper.find('ul').exists()).toBe(true);
        // Запрос не отправлен
        expect(changeNumber).toHaveBeenCalledTimes(0);
        
        // Кликаем по выбору 20
        await li[1].trigger('click');
        expect(li[1].attributes('data-number')).toBe('20');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        // Запрос отправлен с параметром 20
        expect(changeNumber).toHaveBeenCalledTimes(1);
        expect(changeNumber).toHaveBeenCalledWith(20);
    });
});

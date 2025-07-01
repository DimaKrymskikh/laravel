import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import LangugeDropdown from '@/Components/Elements/Dropdowns/LanguageDropdown.vue';
import { useLanguagesListStore } from '@/Stores/languages';
import { app } from '@/Services/app';

app.request = vi.fn();

const languages = [
    {id:1, name: "Английский"},
    {id:11, name: "Китайский"},
    {id:7, name: "Русский"}
];

const getWrapper = function(languageName) {
    return mount(LangugeDropdown, {
            props: {
                languageName,
                setNewLanguageName: vi.fn()
            },
            global: {
                provide: { 
                    languagesList: useLanguagesListStore()
                }
             }
        });
};


describe("@/Components/Elements/Dropdowns/LanguageDropdown.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты, выпадение/сокрытие списка", async () => {
        const wrapper = getWrapper('');
        
        app.request.mockReturnValue(languages);
        expect(app.request).not.toHaveBeenCalled();
        
        const button = wrapper.get('button');
        expect(button.text()).toBe('все');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке первый раз
        await button.trigger('click');
        // Выполнен запрос
        expect(app.request).toHaveBeenCalledTimes(1);
        // Появился список из 4 вариантов
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(4);
        // Первый вариант выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(true);
        // Второй вариант не выбран
        expect(li[1].text()).toBe('Английский');
        expect(li[1].classes('cursor-not-allowed')).toBe(false);
        // Третий вариант не выбран
        expect(li[2].text()).toBe('Китайский');
        expect(li[2].classes('cursor-not-allowed')).toBe(false);
        // Четвёртый вариант не выбран
        expect(li[3].text()).toBe('Русский');
        expect(li[3].classes('cursor-not-allowed')).toBe(false);
        
        // Второй раз кликаем по кнопке
        await button.trigger('click');
        // Запрос не выполняется (по-прежнему, вызов один раз)
        expect(app.request).toHaveBeenCalledTimes(1);
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке третий раз
        await button.trigger('click');
        // Запрос не выполняется (по-прежнему, вызов один раз)
        expect(app.request).toHaveBeenCalledTimes(1);
        // Появился список языков
        expect(wrapper.find('ul').exists()).toBe(true);
    });
    
    it("Изменение языка", async () => {
        const wrapper = getWrapper('Английский');
        
        app.request.mockReturnValue(languages);
        
        const button = wrapper.get('button');
        // На кнопке язык, который задан в начальный момент
        expect(button.text()).toBe('Английский');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке первый раз
        await button.trigger('click');
        // Появился список из 4 вариантов
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(4);
        // Первый вариант не выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(false);
        // Второй вариант выбран
        expect(li[1].text()).toBe('Английский');
        expect(li[1].classes('cursor-not-allowed')).toBe(true);
        // Третий вариант не выбран
        expect(li[2].text()).toBe('Китайский');
        expect(li[2].classes('cursor-not-allowed')).toBe(false);
        // Четвёртый вариант не выбран
        expect(li[3].text()).toBe('Русский');
        expect(li[3].classes('cursor-not-allowed')).toBe(false);
        
        const setNewLanguageName = vi.spyOn(wrapper.vm.props, 'setNewLanguageName');
        expect(setNewLanguageName).not.toHaveBeenCalled();
        
        // Кликаем по четвёртому варианту
        await li[3].trigger('click');
        expect(setNewLanguageName).toHaveBeenCalledTimes(1);
        expect(setNewLanguageName).toHaveBeenCalledWith('Русский');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке второй раз
        await button.trigger('click');
        // Первый вариант не выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(false);
        
        // Кликаем по первому варианту
        await li[0].trigger('click');
        expect(setNewLanguageName).toHaveBeenCalledTimes(2);
        expect(setNewLanguageName).toHaveBeenCalledWith('');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});

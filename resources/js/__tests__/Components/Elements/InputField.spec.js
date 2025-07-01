import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import InputField from '@/components/Elements/InputField.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';

const getWrapper = function(errorsMessage = '') {
    return mount(InputField, {
            props: {
                titleText: 'Поле ввода',
                type: 'text',
                isInputDisabled: undefined,
                isInputAutofocus: false,
                errorsMessage,
                modelValue: ''
            }
        });
};

describe("@/components/Elements/InputField", () => {
    it("Монтирование компоненты InputField (isRequest: false)", async () => {
        const wrapper = getWrapper();

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
        
        // Спиннер отсутствует
        expect(label.findComponent(Spinner).exists()).toBe(false);
        
        // Существует текстовое поле
        const input = label.get('input');
        expect(input.isVisible()).toBe(true);
        expect(input.attributes('type')).toBe('text');
        // Текстовое поле редактируется
        expect(input.element.value).toBe('');
        input.setValue('Некоторое слово');
        expect(input.element.value).toBe('Некоторое слово');
        expect(wrapper.emitted()).toHaveProperty('update:modelValue');
        expect(wrapper.emitted('update:modelValue')[0][0]).toBe('Некоторое слово');
        
//        expect(input.element).toStrictEqual(document.activeElement);
        expect(wrapper.vm.input).toStrictEqual(input.element);
        expect(wrapper.vm.props.isInputAutofocus).toBe(false);
        expect(wrapper.vm.input.value).toBe('Некоторое слово');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Монтирование компоненты InputField (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
        
        // Спиннер присутствует
        const spinner = label.findComponent(Spinner);
        expect(spinner.exists()).toBe(true);
        expect(spinner.props('styleSpinner')).toBe('h-6 fill-gray-700 text-gray-200');
        expect(spinner.classes()).toContain('spinner');
        
        // Текстовое поле заблокировано
        const input = label.get('input');
        expect(input.isVisible()).toBe(true);
        expect(input.attributes('class')).toContain('disabled');
        expect(input.attributes('disabled')).toBe('');
        expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Отрисовка ошибки", async () => {
        const wrapper = getWrapper('Некоторая ошибка');
        
        // Отрисовывается сообщение об ошибке
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(true);
        expect(error.text()).toBe('Некоторая ошибка');
    });
});

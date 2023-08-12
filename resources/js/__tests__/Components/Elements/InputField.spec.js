import { mount } from "@vue/test-utils";

import InputField from '@/components/Elements/InputField.vue';
import Spinner from '@/components/Svg/Spinner.vue';

describe("@/components/Elements/InputField", () => {
    it("Монтирование компоненты InputField (isRequest: false)", async () => {
        const wrapper = mount(InputField, {
            props: {
                titleText: 'Поле ввода',
                type: 'text',
                errorsMessage: '',
                modelValue: '',
                isRequest: false
            }
        });

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
        
        // Спиннер отсутствует
        expect(label.findComponent(Spinner).exists()).toBe(false);
        
        // Существует текстовое поле
        const input = label.find('input');
        expect(input.exists()).toBe(true);
        expect(input.attributes('type')).toBe('text');
        // Текстовое поле редактируется
        expect(input.element.value).toBe('');
        input.setValue('Некоторое слово');
        expect(input.element.value).toBe('Некоторое слово');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Монтирование компоненты InputField (isRequest: true)", async () => {
        const wrapper = mount(InputField, {
            props: {
                titleText: 'Поле ввода',
                type: 'text',
                errorsMessage: '',
                modelValue: '',
                isRequest: true
            }
        });

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
        
        // Спиннер присутствует
        expect(label.findComponent(Spinner).exists()).toBe(true);
        
        // Отсутствует текстовое поле
        const input = label.find('input');
        expect(input.exists()).toBe(false);
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Отрисовка ошибки", async () => {
        const wrapper = mount(InputField, {
            props: {
                titleText: 'Поле ввода',
                type: 'text',
                errorsMessage: 'Некоторая ошибка',
                modelValue: '',
                isRequest: true
            }
        });
        
        // Отрисовывается сообщение об ошибке
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(true);
        expect(error.text()).toBe('Некоторая ошибка');
    });
});

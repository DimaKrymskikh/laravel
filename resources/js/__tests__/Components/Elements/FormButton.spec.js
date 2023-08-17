import { mount } from "@vue/test-utils";

import FormButton from '@/Components/Elements/FormButton.vue';
import Spinner from '@/components/Svg/Spinner.vue';

describe("@/Components/Elements/FormButton.vue", () => {
    it("Отрисовка кнопки формы (isRequest: false)", () => {
        const wrapper = mount(FormButton, {
            props: {
                processing: false,
                isRequest: false,
                text: 'Текст кнопки'
            }
        });
        
        // Текст кнопки присутствует, спиннер отсутствует
        const button = wrapper.get('button');
        const span = button.find('span');
        expect(span.text()).toBe('Текст кнопки');
        expect(button.findComponent(Spinner).exists()).toBe(false);
        // Существует проблема при тестировании
        // Когда processing = false 
        // появляется 
        // [Vue warn]: Failed setting prop "disabled" on <button>: value false is invalid. TypeError: Cannot read properties of null (reading 'name')
        expect(button.attributes('disabled')).toBe(undefined);
    });
    
    it("Отрисовка кнопки формы (isRequest: true)", () => {
        const wrapper = mount(FormButton, {
            props: {
                processing: true,
                isRequest: true,
                text: 'Текст кнопки'
            }
        });
        
        // Текст кнопки отсутствует, спиннер присутствует
        const button = wrapper.get('button');
        const span = button.find('span');
        expect(span.exists()).toBe(false);
        expect(button.findComponent(Spinner).exists()).toBe(true);
        expect(button.attributes('disabled')).toBe('');
    });
});

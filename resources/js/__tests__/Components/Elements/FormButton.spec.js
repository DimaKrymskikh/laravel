import { mount } from "@vue/test-utils";

import FormButton from '@/Components/Elements/FormButton.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';

const getWrapper = function() {
    return mount(FormButton, {
            props: {
                text: 'Текст кнопки'
            }
        });
};

describe("@/Components/Elements/FormButton.vue", () => {
    it("Отрисовка кнопки формы (isRequest: false)", () => {
        const wrapper = getWrapper();
        
        // Текст кнопки присутствует, спиннер отсутствует
        const button = wrapper.get('button');
        expect(button.text()).toBe('Текст кнопки');
        expect(button.findComponent(Spinner).exists()).toBe(false);
        // Атрибут 'disabled' отсутствует
        expect(button.attributes('disabled')).toBe(undefined);
    });
    
    it("Отрисовка кнопки формы (isRequest: true)", () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        // Текст кнопки отсутствует, спиннер присутствует
        const button = wrapper.get('button');
        expect(button.text()).toBe('');
        const spinner = button.findComponent(Spinner);
        expect(button.findComponent(Spinner).exists()).toBe(true);
        expect(spinner.props('styleSpinner')).toBe('h-6 fill-gray-700 text-gray-200');
        expect(spinner.classes()).toContain('flex');
        expect(spinner.classes()).toContain('justify-center');
        // Атрибут 'disabled' присутствует
        expect(button.attributes('disabled')).toBe('');
    });
});

import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import FormButton from '@/Components/Elements/FormButton.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { useAppStore } from '@/Stores/app';

const getWrapper = function(app) {
    return mount(FormButton, {
            props: {
                text: 'Текст кнопки'
            },
            global: {
                provide: { app }
            }
        });
};

describe("@/Components/Elements/FormButton.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка кнопки формы (isRequest: false)", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        // Текст кнопки присутствует, спиннер отсутствует
        const button = wrapper.get('button');
        expect(button.text()).toBe('Текст кнопки');
        expect(button.findComponent(Spinner).exists()).toBe(false);
        // Атрибут 'disabled' отсутствует
        expect(button.attributes('disabled')).toBe(undefined);
    });
    
    it("Отрисовка кнопки формы (isRequest: true)", () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        
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

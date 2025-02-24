import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import { useAppStore } from '@/Stores/app';
import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';

const getWrapper = function(app, modelValue) {
    return mount(Checkbox, {
            props: {
                titleText: 'Поле ввода',
                modelValue
            },
            global: {
                provide: { app }
            }
        });
};

describe("@/components/Elements/Form/Checkbox", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Проверка текста label", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app, false);

        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
    });
    
    it("Монтирование компоненты Checkbox (isRequest: false)", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app, false);
        
        testCheckbox.successChecked(wrapper);
    });
    
    it("Монтирование компоненты Checkbox (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app, false);
        
        testCheckbox.failChecked(wrapper);
    });
});

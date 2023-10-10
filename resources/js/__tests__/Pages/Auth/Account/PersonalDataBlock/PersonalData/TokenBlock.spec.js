import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import TokenBlock from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
import { useAppStore } from '@/Stores/app';

import { checkFormButton } from '@/__tests__/methods/checkFormButton';

const getWrapper = function(app) {
    return mount(TokenBlock, {
            global: {
                provide: { app }
            }
        });
};

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlockBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока токена (isRequest: false)", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.text()).toContain('Токен:');
        expect(wrapper.text()).toContain('Получите токен для взаимодействия с приложением через API.');
        // Проверяем кнопку формы для получения токена
        checkFormButton.checkPropsFormButton(wrapper, 'Получить токен', 'w-56');
        // Можно отправить запрос для получения токена
        checkFormButton.submitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка блока токена (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        expect(wrapper.text()).toContain('Токен:');
        expect(wrapper.text()).toContain('Получите токен для взаимодействия с приложением через API.');
        // Проверяем кнопку формы для получения токена
        checkFormButton.checkPropsFormButton(wrapper, 'Получить токен', 'w-56');
        // Нельзя отправить запрос для получения токена
        checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
});

import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import TokenBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
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
    
    it("Функция handlerGettingToken вызывает form.post с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            onBefore: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper(app);
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        wrapper.vm.handlerGettingToken();
        
        expect(formPost).toHaveBeenCalledTimes(1);
        expect(formPost).toHaveBeenCalledWith('/token', options);
    });
    
    it("Проверка функции onBeforeForHandlerGettingToken", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.onBeforeForHandlerGettingToken();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerGettingToken", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerGettingToken();
        
        expect(app.isRequest).toBe(false);
    });
});

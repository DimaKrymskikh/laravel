import { mount } from "@vue/test-utils";

import EmailBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import { app } from '@/Services/app';

import { checkFormButton } from '@/__tests__/methods/checkFormButton';

const getWrapper = function(email_verified_at = null) {
    return mount(EmailBlock, {
            props: {
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin',
                    email: 'test@example.com',
                    email_verified_at
                }
            }
        });
};

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue", () => {
    it("Отрисовка блока почты (isRequest: false, почта не подтверждена)", async () => {
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        // Проверяем блок эл. почты
        expect(wrapper.text()).toContain('Эл. почта:');
        expect(wrapper.text()).toContain('test@example.com');
        expect(wrapper.text()).toContain('Ваша эл. почта не подтверждена.');
        expect(wrapper.findComponent(CheckSvg).exists()).toBe(false);
        
        // Проверяем кнопку формы для отправки нового письма
        checkFormButton.checkPropsFormButton(wrapper, 'Отправка нового письма', 'w-56');
        // Можно отправить запрос для отправки нового письма
        checkFormButton.submitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка блока почты (isRequest: true, почта не подтверждена)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        // Проверяем блок эл. почты
        expect(wrapper.text()).toContain('Эл. почта:');
        expect(wrapper.text()).toContain('test@example.com');
        expect(wrapper.text()).toContain('Ваша эл. почта не подтверждена.');
        expect(wrapper.findComponent(CheckSvg).exists()).toBe(false);
        
        // Проверяем кнопку формы для отправки нового письма
        checkFormButton.checkPropsFormButton(wrapper, 'Отправка нового письма', 'w-56');
        // Во время запроса нельзя отправить новый запрос
        checkFormButton.notSubmitFormButton(wrapper, formPost);
    });
    
    it("Отрисовка блока почты (почта подтверждена)", async () => {
        const wrapper = getWrapper("2023-05-16T17:11:39.000000Z");
        
        // Проверяем блок эл. почты
        expect(wrapper.text()).toContain('Эл. почта:');
        expect(wrapper.text()).toContain('test@example.com');
        expect(wrapper.text()).not.toContain('Ваша эл. почта не подтверждена.');
        expect(wrapper.findComponent(CheckSvg).exists()).toBe(true);
        
        // Кнопка для отправки нового письма отсутствует
        checkFormButton.isFormButton(wrapper, false);
    });
    
    it("Функция handlerVerifyEmail вызывает form.post с нужными параметрами", () => {
        const options = {
            onBefore: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper();
        
        const formPost = vi.spyOn(wrapper.vm.form, 'post').mockResolvedValue();
        
        wrapper.vm.handlerVerifyEmail();
        
        expect(formPost).toHaveBeenCalledTimes(1);
        expect(formPost).toHaveBeenCalledWith('/verify-email', options);
    });
    
    it("Проверка функции onBeforeForHandlerVerifyEmail", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.onBeforeForHandlerVerifyEmail();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerVerifyEmail", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerVerifyEmail();
        
        expect(app.isRequest).toBe(false);
    });
});

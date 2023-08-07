import { describe, it, expect, vi } from "vitest";
import { mount } from "@vue/test-utils";

import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

describe("@/components/Modal/ForbiddenModal.vue", () => {
    it("Монтирование компоненты ForbiddenModal", async () => {
        const wrapper = mount(ForbiddenModal, {
            props: {
                errors: {
                    message: 'Некоторая ошибка'
                }
            }
        });

        // Модальное окно компоненты ForbiddenModal присутствует
        const forbiddenModal = wrapper.get('#forbidden-modal');
        expect(forbiddenModal.isVisible()).toBe(true);
        
        // Отображается сообщение об ошибке
        const errorsMessage = forbiddenModal.get('#errors-message');
        expect(errorsMessage.text()).toBe('Некоторая ошибка');
        
        // Отображается кнопка 'Закрыть'
        const button = forbiddenModal.get('button');
        expect(button.text()).toBe('Закрыть');
        // После клика по этой кнопке модальное окно закрывается
        await button.trigger('click');
        expect(wrapper.find('#forbidden-modal').exists()).toBe(false);
    });
});

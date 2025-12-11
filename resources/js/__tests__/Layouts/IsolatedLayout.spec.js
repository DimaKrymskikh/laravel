import { mount } from "@vue/test-utils";

import IsolatedLayout from '@/Layouts/IsolatedLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import * as mod from '@/Services/inertia';
import { app } from '@/Services/app';

vi.spyOn(mod, 'useGlobalRequest');

const getWrapper = function() {
    return mount(IsolatedLayout, {});
};

describe("@/Layouts/IsolatedLayout.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование шаблона IsolatedLayout", () => {
        const wrapper = getWrapper();
        
        // Отсутствует компонента GlobalModal
        expect(wrapper.findComponent(GlobalModal).exists()).toBe(false);
    });
    
    it("Видна компонента GlobalModal, т.к. app.isRequest = false", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        const wrapper = getWrapper();

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(true);
    });
    
    it("Компонента GlobalModal не видна, т.к. app.isRequest = true", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        app.isRequest = true;
        const wrapper = getWrapper(true);

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(false);
    });
});

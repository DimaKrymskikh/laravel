import { mount, flushPromises } from "@vue/test-utils";
import { defineComponent } from 'vue';
import { useGlobalRequest } from "@/Services/inertia";

const TestComponent = defineComponent({
    template: '<div></div>',
    setup () {
        return {
            isGlobalRequest: useGlobalRequest()
        };
    }
});

const inertiaStart = new Event('inertia:start');
const inertiaFinish = new Event('inertia:finish');

describe("@/Services/inertia", () => {
    it("Проверка событий 'inertia:start' и 'inertia:finish'", () => {
        const wrapper = mount(TestComponent, {});
        // В начальный момент
        expect(wrapper.vm.isGlobalRequest).toBe(false);
        
        document.dispatchEvent(inertiaStart);
        // Событие 'inertia:start' вызвано (isGlobalRequest изменился)
        expect(wrapper.vm.isGlobalRequest).toBe(true);
        
        document.dispatchEvent(inertiaFinish);
        // Событие 'inertia:start' вызвано (isGlobalRequest изменился)
        expect(wrapper.vm.isGlobalRequest).toBe(false);
    });
    
    it("Размонтирование компоненты снимает события 'inertia:start' и 'inertia:finish' с document", () => {
        const wrapper = mount(TestComponent, {});
        // В начальный момент
        expect(wrapper.vm.isGlobalRequest).toBe(false);
        
        wrapper.unmount();
        
        document.dispatchEvent(inertiaStart);
        // Событие 'inertia:start' не вызвано (isGlobalRequest не изменился)
        expect(wrapper.vm.isGlobalRequest).toBe(false);
        
        wrapper.vm.isGlobalRequest = true;
        document.dispatchEvent(inertiaFinish);
        // Событие 'inertia:finish' не вызвано (isGlobalRequest не изменился)
        expect(wrapper.vm.isGlobalRequest).toBe(true);
    });
});

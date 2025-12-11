import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { messageEmptyTable } from '@/Services/Content/Trials/trials';
import TrialPage from "@/Pages/Auth/Trials/TrialPage.vue";
import IsolatedLayout from '@/Layouts/IsolatedLayout.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import ChooseAnswerModal from '@/Components/Pages/Auth/Trials/TrialPage/ChooseAnswerModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { userAuth } from '@/__tests__/data/users';
import { quizItems } from '@/__tests__/data/quizzes/quizItems';
import { trials } from '@/__tests__/data/quizzes/trials';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn(),
        router: {
            post: vi.fn()
        }
    };
});

const trial = trials[0];

const getWrapper = function() {
    return mount(TrialPage, {
            props: {
                user: userAuth,
                trial,
                quizItems,
                errors: {}
            },
            global: {
                stubs: {
                    ChooseAnswerModal: true
                }
            }
        });
};

describe("@/Pages/Auth/Trials/TrialPage.vue", () => {
    beforeEach(() => {
        vi.useFakeTimers();
    });
    
    afterEach(() => {
        vi.useRealTimers();
    });
    
    it("Отрисовка страницы опроса, который проходит пользователь", () => {
        const wrapper = getWrapper();
        
        const table = wrapper.find('table');
        expect(table.exists()).toBe(true);
        
        const tbody = table.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(trial.answers.length);
        const tds0 = trs[0].findAll('td');
        expect(tds0[2].text()).toBe(trial.answers[0].answer ? trial.answers[0].answer : 'не дан');
        const tds1 = trs[1].findAll('td');
        expect(tds1[2].text()).toBe(trial.answers[1].answer ? trial.answers[1].answer : 'не дан');
        
        const formButton = wrapper.findComponent(FormButton);
        expect(formButton.exists()).toBe(true);
    });
    
    it("Клик по FormButton", async () => {
        const wrapper = getWrapper();
        
        const formButton = wrapper.findComponent(FormButton);
        const button = formButton.find('button');
        
        await button.trigger('click');
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/complete', {
            trial_id: trial.id
        }, {});
    });
    
    it("Проверка onMounted", () => {
        const startTimeInSeconds = trial.lead_time * 60;
        let wrapper = getWrapper();
        
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(startTimeInSeconds);
        expect(vi.getTimerCount()).toBe(1);
        expect(router.post).not.toHaveBeenCalled();
        
        vi.advanceTimersByTime(startTimeInSeconds * 1000);
        wrapper.unmount();
        
        wrapper = getWrapper();
        
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(0);
        expect(vi.getTimerCount()).toBe(0);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/complete', {
            trial_id: trial.id
        }, {});
    });
    
    it("Проверка watch", async () => {
        const startTimeInSeconds = trial.lead_time * 60;
        let wrapper = getWrapper();
        
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(startTimeInSeconds);
        expect(vi.getTimerCount()).toBe(1);
        expect(router.post).not.toHaveBeenCalled();
        
        wrapper.vm.countdown.timeInSeconds.value = startTimeInSeconds - 1;
        await flushPromises();
        expect(router.post).not.toHaveBeenCalled();
        
        wrapper.vm.countdown.timeInSeconds.value = 0;
        await flushPromises();
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/complete', {
            trial_id: trial.id
        }, {});
    });
    
    it("Проверка handlerComplete", () => {
        const wrapper = getWrapper();
        wrapper.vm.handlerComplete();

        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/complete', {
            trial_id: trial.id
        }, {});
    });
    
    it("Клик по PencilSvg открывает модальное окно ChooseAnswerModal", async () => {
        const wrapper = getWrapper();
        expect(wrapper.findComponent(ChooseAnswerModal).exists()).toBe(false);
        
        const pencilSvg = wrapper.findAllComponents(PencilSvg);
        
        await pencilSvg[0].trigger('click');
        expect(wrapper.findComponent(ChooseAnswerModal).exists()).toBe(true);
    });
});

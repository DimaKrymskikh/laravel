import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { messageEmptyTable } from '@/Services/Content/Trials/trials';
import TrialStart from "@/Pages/Auth/Trials/TrialStart.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import LockedButton from '@/Components/Buttons/Variants/LockedButton.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';

import { AuthLayoutStub } from '@/__tests__/stubs/layout';
import { quizWithItems } from '@/__tests__/data/quizzes/quizzes';

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

const getWrapper = function(quiz) {
    return mount(TrialStart, {
            props: {
                errors: {},
                quiz
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                }
            }
        });
};
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe(wrapper.vm.quiz.title);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(3);
    expect(li[0].find('a[href="/"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Главная страница');
    expect(li[1].find('a[href="/trials"]').exists()).toBe(true);
    expect(li[1].text()).toBe('Опросы');
    expect(li[2].find('a').exists()).toBe(false);
    expect(li[2].text()).toBe(wrapper.vm.quiz.title);
};

describe("@/Pages/Auth/Trials/TrialStart.vue", () => {
    it("Отрисовка страницы 'Старт опроса' (нет активных испытаний)", () => {
        const quiz = quizWithItems;
        quiz.isActiveTrial = false;
        
        const wrapper = getWrapper(quiz);
        
        checkH1(wrapper);
        checkBreadCrumb(wrapper);
        
        expect(wrapper.findComponent(LockedButton).exists()).toBe(false);
        expect(wrapper.findComponent(PrimaryButton).exists()).toBe(true);
        expect(wrapper.find('a[href="trials/show_trial"]').exists()).toBe(false);
    });
    
    it("Отрисовка страницы 'Старт опроса' (имеется активное испытание)", () => {
        const quiz = quizWithItems;
        quiz.isActiveTrial = true;
        
        const wrapper = getWrapper(quiz);
        
        checkH1(wrapper);
        checkBreadCrumb(wrapper);
        
        expect(wrapper.findComponent(LockedButton).exists()).toBe(true);
        expect(wrapper.findComponent(PrimaryButton).exists()).toBe(false);
        expect(wrapper.find('a[href="/trials/show_trial"]').exists()).toBe(true);
    });
    
    it("Клик по кнопке, которая начинает испытание", async () => {
        const quiz = quizWithItems;
        quiz.isActiveTrial = false;
        
        const wrapper = getWrapper(quiz);
        
        const primaryButton = wrapper.getComponent(PrimaryButton);
        await primaryButton.trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/start', {
                quiz_id: quiz.id
            }, {});
    });
});

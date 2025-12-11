import '@/bootstrap';
import { mount } from "@vue/test-utils";

import TrialResults from "@/Pages/Auth/Account/TrialResults.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import { messageEmptyTableForResults } from '@/Services/Content/Trials/trials';
import { trials } from '@/__tests__/data/quizzes/trials';
import { userAuth } from '@/__tests__/data/users';

import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(trials = []) {
    return mount(TrialResults, {
            props: {
                user: userAuth,
                trials,
                errors: null
            },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub
                }
            }
        });
};

describe("@/Pages/Auth/Account/TrialResults.vue", () => {
    it("Отрисовка TrialResults с опросами", () => {
        const wrapper = getWrapper(trials);
        
        expect(wrapper.text()).not.toContain(messageEmptyTableForResults);
    });
    
    it("Отрисовка TrialResults без опросов", () => {
        const wrapper = getWrapper();
        
        expect(wrapper.text()).toContain(messageEmptyTableForResults);
    });
});

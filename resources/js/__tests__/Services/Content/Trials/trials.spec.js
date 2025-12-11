import { trialQuestions } from '@/Services/Content/Trials/trials';
import { trials } from '@/__tests__/data/quizzes/trials';

const trial = trials[0];
const answer = trial.answers[0];

describe("@/Services/Content/Trials/trials", () => {
    beforeEach(() => {
        trialQuestions.isShow = false;
        trialQuestions.activeQuestion = undefined;
    });
    
    it("trialQuestions.show()", () => {
        trialQuestions.show(answer);
        expect(trialQuestions.isShow).toBe(true);
        expect(trialQuestions.activeQuestion).toStrictEqual(answer);
    });
    
    it("trialQuestions.hide()", () => {
        trialQuestions.isShow = true;
        trialQuestions.hide();
        expect(trialQuestions.isShow).toBe(false);
    });
});

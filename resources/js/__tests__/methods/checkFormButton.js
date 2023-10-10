import FormButton from '@/Components/Elements/FormButton.vue';

const isFormButton = function(wrapper, isItem) {
    expect(wrapper.findComponent(FormButton).exists()).toBe(isItem);
};

const checkPropsFormButton = function(wrapper, text, width) {
    const formButton = wrapper.get('form').getComponent(FormButton);
    expect(formButton.isVisible()).toBe(true);
    expect(formButton.props('text')).toBe(text);
    expect(formButton.classes()).toContain(width);
};

const submitFormButton = async function(wrapper, spy) {
    const formTag = wrapper.get('form');
    const formButton = formTag.getComponent(FormButton);
    const button = formButton.get('button');

    expect(spy).not.toHaveBeenCalled();
    await button.trigger('submit');
    expect(spy).toHaveBeenCalledTimes(1);
};

const notSubmitFormButton = async function(wrapper, spy) {
    const formTag = wrapper.get('form');
    const formButton = formTag.getComponent(FormButton);
    const button = formButton.get('button');

    expect(spy).not.toHaveBeenCalled();
    await button.trigger('submit');
    expect(spy).not.toHaveBeenCalled();
};

export const checkFormButton = {
    isFormButton,
    checkPropsFormButton,
    submitFormButton,
    notSubmitFormButton
};

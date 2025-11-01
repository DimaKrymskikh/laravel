import Textarea from '@/Components/Elements/Form/Textarea/Textarea.vue';
import Spinner from '@/Components/Svg/Spinner.vue';

const findNumberOfTextareasOnPage = function(wrapper, numberOfTextareas) {
        const textareas = wrapper.findAllComponents(Textarea);
        expect(textareas.length).toBe(numberOfTextareas);
        return textareas;
};

const checkPropsTextarea = function(wrapper, titleText, modelValue, errorsMessage = undefined, isTextareaAutofocus = undefined, isTextareaDisabled = undefined) {
    expect(wrapper.props('titleText')).toBe(titleText);
    expect(wrapper.props('modelValue')).toBe(modelValue);
    expect(wrapper.props('errorsMessage')).toBe(errorsMessage);
    expect(wrapper.props('isTextareaAutofocus')).toBe(isTextareaAutofocus);
    expect(wrapper.props('isTextareaDisabled')).toBe(isTextareaDisabled);
};

const checkTextareaWhenThereIsNoRequest = function(wrapper, valueBegin, valueEnd, item = 0) {
    expect(wrapper.findComponent(Spinner).exists()).toBe(false);
    
    // Поле ввода заполняется
    const textarea = wrapper.get('textarea');
    expect(textarea.element.value).toBe(valueBegin);
    textarea.setValue(valueEnd);
    expect(wrapper.emitted()).toHaveProperty('update:modelValue');
    expect(wrapper.emitted('update:modelValue')[item][0]).toBe(valueEnd);
};

const checkTextareaWhenRequestIsMade = function(wrapper, valueBegin, valueEnd) {
    expect(wrapper.findComponent(Spinner).exists()).toBe(true);
    
    // Поле ввода не заполняется
    const textarea = wrapper.get('textarea');
    expect(textarea.element.value).toBe(valueBegin);
    textarea.setValue(valueEnd);
    expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
};

export const checkTextareas = {
    findNumberOfTextareasOnPage,
    checkPropsTextarea,
    checkTextareaWhenThereIsNoRequest,
    checkTextareaWhenRequestIsMade
};

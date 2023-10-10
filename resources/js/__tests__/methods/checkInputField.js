import InputField from '@/components/Elements/InputField.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const findNumberOfInputFieldOnPage = function(wrapper, numberOfInputField) {
        const inputFields = wrapper.findAllComponents(InputField);
        expect(inputFields.length).toBe(numberOfInputField);
        return inputFields;
};

const checkPropsInputField = function(wrapper, titleText, type, errorsMessage, modelValue, isInputAutofocus = undefined, isInputDisabled = undefined) {
    expect(wrapper.props('titleText')).toBe(titleText);
    expect(wrapper.props('type')).toBe(type);
    expect(wrapper.props('errorsMessage')).toBe(errorsMessage);
    expect(wrapper.props('modelValue')).toBe(modelValue);
    expect(wrapper.props('isInputAutofocus')).toBe(isInputAutofocus);
    expect(wrapper.props('isInputDisabled')).toBe(isInputDisabled);
};

const checkInputFieldWhenThereIsNoRequest = function(wrapper, valueBegin, valueEnd, item = 0) {
    expect(wrapper.findComponent(Spinner).exists()).toBe(false);
    
    // Поле ввода заполняется
    const input = wrapper.get('input');
    expect(input.element.value).toBe(valueBegin);
    input.setValue(valueEnd);
    expect(wrapper.emitted()).toHaveProperty('update:modelValue');
    expect(wrapper.emitted('update:modelValue')[item][0]).toBe(valueEnd);
};

const checkInputFieldWhenRequestIsMade = function(wrapper, valueBegin, valueEnd) {
    expect(wrapper.findComponent(Spinner).exists()).toBe(true);
    
    // Поле ввода не заполняется
    const input = wrapper.get('input');
    expect(input.element.value).toBe(valueBegin);
    input.setValue(valueEnd);
    expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
};

export const checkInputField = {
    findNumberOfInputFieldOnPage,
    checkPropsInputField,
    checkInputFieldWhenThereIsNoRequest,
    checkInputFieldWhenRequestIsMade
};

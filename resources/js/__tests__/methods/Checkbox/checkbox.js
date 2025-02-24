const successChecked = async function(checkbox) {
    const input = checkbox.get('input');
    expect(input.attributes('type')).toBe('checkbox');
    expect(input.attributes('disabled')).toBe(undefined);
    
    await input.setValue(true);
    await input.setValue(false);
    expect(checkbox.emitted()).toHaveProperty('update:modelValue');
    expect(checkbox.emitted('update:modelValue')[0][0]).toBe(true);
    expect(checkbox.emitted('update:modelValue')[1][0]).toBe(false);
};

const failChecked = async function(checkbox) {
    const input = checkbox.get('input');
    expect(input.attributes('type')).toBe('checkbox');
    expect(input.attributes('disabled')).toBe('');
    
    await input.setValue(true);
    expect(checkbox.emitted()).not.toHaveProperty('update:modelValue');
};

const existsRememberMe = function(checkbox) {
    expect(checkbox.exists()).toBe(true);
    expect(checkbox.props('titleText')).toBe("Запомнить меня:");
};

export {
    successChecked,
    failChecked,
    existsRememberMe
};

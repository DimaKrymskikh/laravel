import BaseModal from '@/components/Modal/Request/BaseModal.vue';

const getBaseModal = function(wrapper) {
    return wrapper.getComponent(BaseModal);
};

const checkPropsBaseModal = function(wrapper, headerTitle, hideModal, handlerSubmit = undefined) {
    expect(wrapper.props('headerTitle')).toBe(headerTitle);
    expect(wrapper.props('hideModal')).toBe(hideModal);
    expect(wrapper.props('handlerSubmit')).toBe(handlerSubmit);
};

const absenceOfHandlerSubmit = function(wrapper) {
    const modalNo = wrapper.get('#modal-no');
    expect(modalNo.text()).toBe('Закрыть');
    const modalYes = wrapper.find('#modal-yes');
    expect(modalYes.exists()).toBe(false);
};

const presenceOfHandlerSubmit = function(wrapper) {
    const modalNo = wrapper.get('#modal-no');
    expect(modalNo.text()).toBe('Нет');
    const modalYes = wrapper.find('#modal-yes');
    expect(modalYes.exists()).toBe(true);
};

export const hideBaseModal = async function(wrapper, mockFunction) {
    // Клик по кнопке 'Нет' закрывает модальное окно
    const modalNo = wrapper.get('#modal-no');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalNo.trigger('click');
    expect(mockFunction).toHaveBeenCalledTimes(1);

    mockFunction.mockClear();

    // Клик по крестику закрывает модальное окно
    const modalCross = wrapper.get('#modal-cross');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalCross.trigger('click');
    expect(mockFunction).toHaveBeenCalledTimes(1);

    mockFunction.mockClear();

    // Клик по заднему фону закрывает модальное окно
    const modalBackground = wrapper.get('#modal-background');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalBackground.trigger('click');
    expect(mockFunction).toHaveBeenCalledTimes(1);

    mockFunction.mockClear();
};

export const notHideBaseModal = async function(wrapper, mockFunction) {
    // Клик по кнопке 'Нет' не закрывает модальное окно
    const modalNo = wrapper.get('#modal-no');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalNo.trigger('click');
    expect(mockFunction).not.toHaveBeenCalled();

    // Клик по крестику не закрывает модальное окно
    const modalCross = wrapper.get('#modal-cross');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalCross.trigger('click');
    expect(mockFunction).not.toHaveBeenCalled();

    // Клик по заднему фону не закрывает модальное окно
    const modalBackground = wrapper.get('#modal-background');
    expect(mockFunction).not.toHaveBeenCalled();
    await modalBackground.trigger('click');
    expect(mockFunction).not.toHaveBeenCalled();
};

export const submitRequestInBaseModal = async function(wrapper, mockFunction) {
    // Кнопка 'Да' не содержит класс 'disabled'
    const modalYes = wrapper.get('#modal-yes');
    expect(modalYes.classes('disabled')).toBe(false);
    // Клик по кнопке 'Да' отправляет запрос на сервер
    expect(mockFunction).not.toHaveBeenCalled();
    await modalYes.trigger('click');
    expect(mockFunction).toHaveBeenCalledTimes(1);

    mockFunction.mockClear();
};

export const notSubmitRequestInBaseModal = async function(wrapper, mockFunction) {
    // Кнопка 'Да' содержит класс 'disabled'
    const modalYes = wrapper.get('#modal-yes');
    expect(modalYes.classes('disabled')).toBe(true);
    // Клик по кнопке 'Да' не отправляет запрос на сервер
    expect(mockFunction).not.toHaveBeenCalled();
    await modalYes.trigger('click');
    expect(mockFunction).not.toHaveBeenCalled();
};

export const checkBaseModal = {
    getBaseModal,
    checkPropsBaseModal,
    absenceOfHandlerSubmit,
    presenceOfHandlerSubmit,
    hideBaseModal,
    notHideBaseModal,
    submitRequestInBaseModal,
    notSubmitRequestInBaseModal
};

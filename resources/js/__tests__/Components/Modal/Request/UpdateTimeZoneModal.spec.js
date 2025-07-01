import { mount, flushPromises } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import UpdateTimeZoneModal from '@/Components/Modal/Request/UpdateTimeZoneModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventTargetClassListContainsFalseAndGetAttribute8 } from '@/__tests__/fake/Event';
import { timezones_nov } from '@/__tests__/data/timezones';

vi.mock('@inertiajs/vue3');

app.request = vi.fn();
const hideModal = vi.spyOn(city, 'hideUpdateTimeZoneModal');

const getWrapper = function() {
    return mount(UpdateTimeZoneModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.cityTimeZone).toBe('');
    expect(wrapper.vm.errorsName).toBe('');
    expect(wrapper.vm.timezones).toBe(null);
    
    expect(wrapper.text()).toContain('Введите хотя бы три символа для получения временных поясов');
    expect(wrapper.text()).not.toContain('Ничего не найдено');
};

const renderTimeZonesList = function(wrapper) {
    expect(wrapper.text()).not.toContain('Введите хотя бы три символа для получения временных поясов');
    expect(wrapper.text()).not.toContain('Ничего не найдено');
    // Появляется список временных зон
    const ul = wrapper.find('ul');
    expect(ul.exists()).toBe(true);
    const lis = ul.findAll('li');
    expect(lis.length).toBe(5);
    // Кнопка удаления временного пояса
    expect(lis[0].text()).toBe('Убрать временной пояс');
    expect(lis[0].attributes('data-id')).toBe('0');
    // 1
    expect(lis[1].text()).toBe('Africa/Porto-Novo');
    expect(lis[1].attributes('data-id')).toBe('48');
    // 2
    expect(lis[2].text()).toBe('Asia/Novokuznetsk');
    expect(lis[2].attributes('data-id')).toBe('258');
    // 3
    expect(lis[3].text()).toBe('Asia/Novosibirsk');
    expect(lis[3].attributes('data-id')).toBe('259');
    // 4
    expect(lis[4].text()).toBe('Europe/Ulyanovsk');
    expect(lis[4].attributes('data-id')).toBe('361');
    
    // Возвращается кнопка 'Asia/Novosibirsk'
    return lis[3];
};
        
describe("@/Components/Modal/Request/UpdateTimeZoneModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты UpdateTimeZoneModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Временной пояс города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityTimeZone, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], city.timeZone, 'asia');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, `Изменение временного пояса города ${city.name}`, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, hideModal);
    });
    
    it("Монтирование компоненты UpdateTimeZoneModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Временной пояс города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityTimeZone, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.cityTimeZone, 'asia');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, `Изменение временного пояса города ${city.name}`, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.notHideBaseModal(baseModal, hideModal);
    });
    
    it("Получение списка временных поясов", async () => {
        const appRequest = vi.spyOn(app, 'request');
        
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Временной пояс города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityTimeZone, true);
        
        // В поле input вводим 1 символ
        // Запрос не отправляется
        expect(appRequest).not.toHaveBeenCalled();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'a');
        await flushPromises();
        expect(appRequest).not.toHaveBeenCalled();
        // В поле input вводим второй символ
        // Запрос не отправляется
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'as', 1);
        await flushPromises();
        expect(appRequest).not.toHaveBeenCalled();
        // В поле input вводим третий символ
        // Отправляется запрос
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'asi', 2);
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(1);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
        // В поле input убираем один символ (остаются 2)
        // Запрос не отправляется
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'as', 3);
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(1);
        // В поле input вводим третий символ
        // Отправляется запрос
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'asi', 4);
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(2);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
        // В поле input вводим четвёртый символ
        // Отправляется запрос
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'asia', 5);
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(3);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
    });
    
    it("Список временных поясов: []" , async () => {
        const appRequest = vi.spyOn(app, 'request').mockResolvedValue([]);
        
        const wrapper = getWrapper();
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        
        // Вводим 3 символа
        expect(appRequest).not.toHaveBeenCalled();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'qqq');
        await flushPromises();
        // Отправляется запрос
        expect(appRequest).toHaveBeenCalledTimes(1);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
        // Появляется запись 'Ничего не найдено'
        expect(wrapper.text()).not.toContain('Введите хотя бы три символа для получения временных поясов');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Список временных поясов: timezones_nov; запрос на изменение временного пояса" , async () => {
        const appRequest = vi.spyOn(app, 'request').mockResolvedValue(timezones_nov);
        
        const wrapper = getWrapper();
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        
        // Вводим 3 символа
        expect(appRequest).not.toHaveBeenCalled();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'abc');
        await flushPromises();
        // Отправляется запрос
        expect(appRequest).toHaveBeenCalledTimes(1);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
        
        const activeLi = renderTimeZonesList(wrapper);
        
        // Клик по кнопке 'Asia/Novosibirsk'
        expect(router.put).not.toHaveBeenCalled();
        await activeLi.trigger('click');
        // Отправляется запрос на изменение временного пояса
        expect(router.put).toHaveBeenCalledTimes(1);
    });
    
    it("Блокировка запроса на изменение временного пояса (isRequst: true)" , async () => {
        const appRequest = vi.spyOn(app, 'request').mockResolvedValue(timezones_nov);
        
        const wrapper = getWrapper();
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        
        // Вводим 3 символа
        expect(appRequest).not.toHaveBeenCalled();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityTimeZone, 'abc');
        await flushPromises();
        // Отправляется запрос
        expect(appRequest).toHaveBeenCalledTimes(1);
        expect(appRequest).toHaveBeenCalledWith(`/admin/timezone?name=${wrapper.vm.cityTimeZone}`, 'GET');
        
        const activeLi = renderTimeZonesList(wrapper);
        
        // Имитируем выполнение запроса
        app.isRequest = true;
        await flushPromises();
        
        // Клик по кнопке 'Asia/Novosibirsk'
        expect(router.put).not.toHaveBeenCalled();
        await activeLi.trigger('click');
        // Запрос на изменение временного пояса не отправляется
        expect(router.put).not.toHaveBeenCalled();
    });
    
    it("Функция handlerUpdateTimeZone вызывает router.put с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerUpdateTimeZone(eventTargetClassListContainsFalseAndGetAttribute8);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`cities/${city.id}/timezone/${eventTargetClassListContainsFalseAndGetAttribute8.target.getAttribute('data-id')}`, {
                timezone_id: city.timeZone
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateTimeZone", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsName = 'ErrorName';
        wrapper.vm.onBeforeForHandlerUpdateTimeZone();
        
        expect(wrapper.vm.app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateTimeZone", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateTimeZone();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateTimeZone", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateTimeZone({ name: 'ErrorName' });
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onFinishForHandlerUpdateTimeZone", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerUpdateTimeZone();
        
        expect(app.isRequest).toBe(false);
    });
});

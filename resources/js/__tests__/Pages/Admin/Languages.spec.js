import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import Languages from "@/Pages/Admin/Languages.vue";
import RemoveLanguageModal from '@/Components/Modal/Request/Languages/RemoveLanguageModal.vue';
import UpdateLanguageModal from '@/Components/Modal/Request/Languages/UpdateLanguageModal.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

import { languages } from '@/__tests__/data/languages';
import { AdminLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(languages = []) {
    return mount(Languages, {
            props: {
                errors: {},
                languages
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub,
                    RemoveLanguageModal: true,
                    UpdateLanguageModal: true
                }
            }
        });
};
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Языки');
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe('Языки');
};

describe("@/Pages/Admin/Languages.vue", () => {
    it("Отрисовка страницы 'Языки' при наличии языков", async () => {
        const wrapper = getWrapper(languages);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Проверяем таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain('Ещё ни один язык не добавлен');
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(4);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Язык');
        expect(th[2].text()).toBe('');
        expect(th[3].text()).toBe('');
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(2);
        expect(tr[0].text()).toContain('Английский');
        expect(tr[1].text()).toContain('Русский');
        
        const tds = tr[1].findAll('td');
        expect(tds.length).toBe(4);
        expect(tds[0].text()).toBe('2');
        expect(tds[1].text()).toBe('Русский');
        expect(tds[2].getComponent(PencilSvg).props('title')).toBe('Редактировать язык');
        expect(tds[3].getComponent(TrashSvg).props('title')).toBe('Удалить язык');
    });
    
    it("Отрисовка страницы 'Языки' без языков", () => {
        const wrapper = getWrapper();
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Таблица городов отсутствует
        const table = wrapper.find('table');
        expect(table.exists()).toBe(false);
        // На странице присутствует запись
        expect(wrapper.text()).toContain('Ещё ни один язык не добавлен');
    });
    
    it("Проверка появления модальных окон", async () => {
        const wrapper = getWrapper(languages);
        
        // Находим таблицу 
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        
        // В теле таблицы берём одну строку
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(2);
        const activeTr = tr[1];
        expect(activeTr.text()).toContain('Русский');
        
        // Проверяем модальное окно 'Удаление языка'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(RemoveLanguageModal).exists()).toBe(false);
        const trashSvg = activeTr.getComponent(TrashSvg);
        await trashSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(RemoveLanguageModal).exists()).toBe(true);
        
        // Проверяем модальное окно 'Изменение названия языка'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateLanguageModal).exists()).toBe(false);
        const cityPencilSvg = activeTr.getComponent(PencilSvg);
        await cityPencilSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(UpdateLanguageModal).exists()).toBe(true);
    });
});

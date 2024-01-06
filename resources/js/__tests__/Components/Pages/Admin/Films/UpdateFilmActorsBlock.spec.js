import { flushPromises, mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import UpdateFilmActorsBlock from '@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { json_film_actors, json_free_actors } from '@/__tests__/data/actors';

const hideUpdateFilmActorsModal = vi.fn();
const updateFilm = {
    id: 8,
    title: 'Бриллиантовая рука',
    fieldValue: 'Жулики хотят вернуть себе бриллианты'
};

const getWrapper = function(app, filmsAdmin) {
    return mount(UpdateFilmActorsBlock, {
            props: {
                updateFilm,
                hideUpdateFilmActorsModal,
                isShowUpdateFilmActorsModal: true
            },
            global: {
                provide: { app, filmsAdmin }
            }
        });
};

describe("@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока UpdateFilmActorsBlock", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin);
        
        const updateFilmActorsModal = wrapper.getComponent(UpdateFilmActorsModal);
        expect(updateFilmActorsModal.props('updateFilm')).toBe(wrapper.vm.props.updateFilm);
        expect(updateFilmActorsModal.props('hideUpdateFilmActorsModal')).toBe(wrapper.vm.props.hideUpdateFilmActorsModal);
        expect(updateFilmActorsModal.props('showRemoveActorFromFilmModal')).toBe(wrapper.vm.showRemoveActorFromFilmModal);
        
        expect(wrapper.findComponent(RemoveActorFromFilmModal).exists()).toBe(false);
    });
    
    it("Функция hideRemoveActorFromFilmModal изменяет isRemoveActorFromFilmModal с true на false", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin);
        
        wrapper.vm.isRemoveActorFromFilmModal = true;
        wrapper.vm.hideRemoveActorFromFilmModal();
        expect(wrapper.vm.isRemoveActorFromFilmModal).toBe(false);
    });
    
    it("Функция showRemoveActorFromFilmModal открывает модальное окно RemoveActorFromFilmModal и правильно определяет данные удаляемого актёра", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
        
        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        const updateFilmActorsModal = wrapper.getComponent(UpdateFilmActorsModal);
        
        const uls = updateFilmActorsModal.findAll('ul');
        expect(uls.length).toBe(2);
        
        const filmActorsUl = uls[0];
        const filmActorsLis = filmActorsUl.findAll('li');
        expect(filmActorsLis.length).toBe(json_film_actors.actors.length);
        // Клик по актёру фильма открывает окно для удаления этого актёра из фильма
        await filmActorsLis[1].trigger('click');
        const removeActorFromFilmModal = wrapper.getComponent(RemoveActorFromFilmModal);
        // Правильно определяются данные удаляемого актёра
        expect(parseInt(wrapper.vm.removeActor.id, 10)).toBe(json_film_actors.actors[1].id);
        expect(wrapper.vm.removeActor.first_name).toBe(json_film_actors.actors[1].first_name);
        expect(wrapper.vm.removeActor.last_name).toBe(json_film_actors.actors[1].last_name);
        // Проверка props
        expect(removeActorFromFilmModal.props('removeActor')).toBe(wrapper.vm.removeActor);
        expect(removeActorFromFilmModal.props('updateFilm')).toBe(wrapper.vm.props.updateFilm);
        expect(removeActorFromFilmModal.props('hideRemoveActorFromFilmModal')).toBe(wrapper.vm.hideRemoveActorFromFilmModal);
    });
});

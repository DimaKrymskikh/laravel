import { flushPromises, mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import { app } from '@/Services/app';
import { film, removeActor } from '@/Services/Content/films';
import UpdateFilmActorsBlock from '@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { json_film_actors, json_free_actors } from '@/__tests__/data/actors';

const hideUpdateFilmActorsModal = vi.fn();
const updateFilm = {
    id: 8,
    title: 'Бриллиантовая рука',
    fieldValue: 'Жулики хотят вернуть себе бриллианты'
};

const getWrapper = function() {
    return mount(UpdateFilmActorsBlock, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

describe("@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
    });
    
    it("Отрисовка блока UpdateFilmActorsBlock", async () => {
        // Компонента монтируется с открытым модальным окном.
        film.showUpdateFilmActorsModal();
        
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmActorsModal).exists()).toBe(true);
        expect(wrapper.findComponent(RemoveActorFromFilmModal).exists()).toBe(false);
        
        film.hideUpdateFilmActorsModal();
        removeActor.showRemoveActorFromFilmModal();
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmActorsModal).exists()).toBe(false);
        expect(wrapper.findComponent(RemoveActorFromFilmModal).exists()).toBe(true);
    });
});

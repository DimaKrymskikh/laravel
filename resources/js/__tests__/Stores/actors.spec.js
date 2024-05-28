import { setActivePinia, createPinia } from 'pinia';

import { useActorsListStore } from '@/Stores/actors';

describe("@/Stores/actors", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Хранилище actors сохраняет изменения", () => {
        const actorsList = useActorsListStore();
        
        expect(actorsList.page).toBe(1); 
        expect(actorsList.perPage).toBe(20); 
        expect(actorsList.name).toBe(''); 
        
        actorsList.page = 5;
        actorsList.perPage = 25;
        actorsList.name = 'Иван';
        
        expect(actorsList.page).toBe(5); 
        expect(actorsList.perPage).toBe(25); 
        expect(actorsList.name).toBe('Иван'); 
    });
    
    it("getUrl задаёт url пагинации", () => {
        const actorsList = useActorsListStore();
        
        actorsList.perPage = 5;
        actorsList.name = 'Иван';
        
        const url = actorsList.getUrl();
        expect(url).toBe('/admin/actors?page=1&number=5&name=Иван'); 
        
        const urlWithId = actorsList.getUrl(77);
        expect(urlWithId).toBe('/admin/actors/77?page=1&number=5&name=Иван'); 
    });
});

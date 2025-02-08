import { setActivePinia, createPinia } from 'pinia';

import { useFilmsListStore } from '@/Stores/films';
import { useFilmsAccountStore } from '@/Stores/films';
import { useFilmsAdminStore } from '@/Stores/films';

describe("@/Stores/films", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Хранилище films задаёт разные объекты", () => {
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        const filmsAdmin = useFilmsAdminStore();
        
        filmsList.perPage = 5;
        filmsList.title = 'ab';
        filmsList.description = 'x y';
        filmsList.release_year = 2020;
        
        filmsAccount.page = 7;
        filmsAccount.title = 'title';
        filmsAccount.release_year = 2024;
        
        filmsAdmin.page = 12;
        filmsAdmin.perPage = 250;
        filmsAdmin.description = 'des tion';
        
        expect(filmsList.page).toBe(1); 
        expect(filmsList.perPage).toBe(5); 
        expect(filmsList.title).toBe('ab'); 
        expect(filmsList.description).toBe('x y'); 
        expect(filmsList.release_year).toBe(2020); 
        
        expect(filmsAccount.page).toBe(7); 
        expect(filmsAccount.perPage).toBe(20); 
        expect(filmsAccount.title).toBe('title'); 
        expect(filmsAccount.description).toBe(''); 
        expect(filmsAccount.release_year).toBe(2024); 
        
        expect(filmsAdmin.page).toBe(12); 
        expect(filmsAdmin.perPage).toBe(250); 
        expect(filmsAdmin.title).toBe(''); 
        expect(filmsAdmin.description).toBe('des tion'); 
        expect(filmsAdmin.release_year).toBe(''); 
    });
    
    it("resetSearchFilter очищает параметры фильтра", () => {
        const filmsList = useFilmsListStore();
        
        filmsList.perPage = 5;
        filmsList.title = 'ab';
        filmsList.description = 'x y';
        filmsList.release_year = 2020;
        
        filmsList.resetSearchFilter();
        
        // Параметры пагинации не изменились
        expect(filmsList.page).toBe(1); 
        expect(filmsList.perPage).toBe(5);
        // Параметры фильтра поиска очищены
        expect(filmsList.title).toBe(''); 
        expect(filmsList.description).toBe(''); 
        expect(filmsList.release_year).toBe(''); 
    });
    
    it("getUrl задаёт url пагинации", () => {
        const filmsList = useFilmsListStore();
        
        filmsList.perPage = 5;
        filmsList.title = 'ab';
        filmsList.description = 'x y';
        filmsList.release_year = 2020;
        
        const url = filmsList.getUrl('/films');
        
        expect(url).toBe('/films?page=1&number=5&title_filter=ab&description_filter=x y&release_year_filter=2020'); 
    });
    
    it("setOptions изменяет параметры фильма", () => {
        const filmsList = useFilmsListStore();
        
        const films = {
            current_page: 11,
            per_page: 50
        };
        window.location.search = '/films?page=3&number=10&title_filter=ab&description_filter=xyz&release_year_filter=2025';
        
        filmsList.setOptions(films);
        
        expect(filmsList.page).toBe(films.current_page); 
        expect(filmsList.perPage).toBe(films.per_page); 
        expect(filmsList.title).toBe('ab'); 
        expect(filmsList.description).toBe('xyz'); 
        expect(filmsList.release_year).toBe('2025'); 
    });
});

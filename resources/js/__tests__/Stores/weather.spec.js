import { setActivePinia, createPinia } from 'pinia';

import { useWeatherPageAuthStore } from '@/Stores/weather';

describe("@/Stores/weather", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Хранилище weather сохраняет изменения", () => {
        const weatherPageAuth = useWeatherPageAuthStore();
        
        expect(weatherPageAuth.page).toBe(1); 
        expect(weatherPageAuth.perPage).toBe(20); 
        expect(weatherPageAuth.datefrom).toBe(''); 
        expect(weatherPageAuth.dateto).toBe(''); 
        
        weatherPageAuth.page = 12;
        weatherPageAuth.perPage = 500;
        weatherPageAuth.datefrom = 2021;
        weatherPageAuth.dateto = 2025;
        
        expect(weatherPageAuth.page).toBe(12); 
        expect(weatherPageAuth.perPage).toBe(500); 
        expect(weatherPageAuth.datefrom).toBe(2021); 
        expect(weatherPageAuth.dateto).toBe(2025); 
    });
    
    it("getUrl задаёт url пагинации", () => {
        const weatherPageAuth = useWeatherPageAuthStore();
        
        weatherPageAuth.page = 12;
        weatherPageAuth.perPage = 500;
        weatherPageAuth.datefrom = 2021;
        weatherPageAuth.dateto = 2025;
        
        const url = weatherPageAuth.getUrl('/weather');
        
        expect(url).toBe('/weather?page=12&number=500&datefrom=2021&dateto=2025'); 
    });
});

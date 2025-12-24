import { paginationOptionsForWeatherLogs } from '@/Services/Content/Weather/weatherLogs';

describe("@/Services/Content/Weather/weatherLogs", () => {
    beforeEach(() => {
        paginationOptionsForWeatherLogs.datefrom = '';
        paginationOptionsForWeatherLogs.dateto = '';
    });
    
    it("paginationOptionsForWeatherLogs.urlParams() (window.location.search = null)", () => {
        window.location.search = null;
        paginationOptionsForWeatherLogs.datefrom = '01.01.2025';
        paginationOptionsForWeatherLogs.dateto = '31.01.2025';
        paginationOptionsForWeatherLogs.urlParams();
        
        expect(paginationOptionsForWeatherLogs.datefrom).toBe('');
        expect(paginationOptionsForWeatherLogs.dateto).toBe('');
    });
    
    it("paginationOptionsForWeatherLogs.urlParams() (window.location.search not null)", () => {
        window.location.search = '?datefrom=01.01.2024&dateto=31.01.20024';
        paginationOptionsForWeatherLogs.urlParams();
        
        expect(paginationOptionsForWeatherLogs.datefrom).toBe('01.01.2024');
        expect(paginationOptionsForWeatherLogs.dateto).toBe('31.01.20024');
    });
});

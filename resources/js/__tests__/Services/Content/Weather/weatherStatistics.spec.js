import { statisticsOptions, periodicityIntervals } from '@/Services/Content/Weather/weatherStatistics';

describe("@/Services/Content/Weather/weatherStatistics", () => {
    beforeEach(() => {
        statisticsOptions.datefrom = '';
        statisticsOptions.dateto = '';
        statisticsOptions.interval = '';
    });
    
    it("statisticsOptions.clearDatefrom()", () => {
        statisticsOptions.datefrom = '01.01.2025';
        statisticsOptions.clearDatefrom();
        expect(statisticsOptions.datefrom).toBe('');
    });
    
    it("statisticsOptions.clearDateto()", () => {
        statisticsOptions.dateto = '01.01.2025';
        statisticsOptions.clearDateto();
        expect(statisticsOptions.dateto).toBe('');
    });
    
    it("statisticsOptions.getIntervalText(interval)", () => {
        for(const index in periodicityIntervals) {
            expect(['день', 'неделя', 'месяц', 'год']).toContain(statisticsOptions.getIntervalText(periodicityIntervals[index]));
        }
    });
    
    it("statisticsOptions.urlParams() (window.location.search = null)", () => {
        window.location.search = null;
        statisticsOptions.datefrom = '01.01.2025';
        statisticsOptions.dateto = '31.01.2025';
        statisticsOptions.interval = 'week';
        statisticsOptions.urlParams();
        
        expect(statisticsOptions.datefrom).toBe('');
        expect(statisticsOptions.dateto).toBe('');
        expect(statisticsOptions.interval).toBe('');
    });
    
    it("statisticsOptions.urlParams() (window.location.search not null)", () => {
        window.location.search = '?datefrom=01.01.2024&dateto=31.01.20024&interval=day';
        statisticsOptions.urlParams();
        
        expect(statisticsOptions.datefrom).toBe('01.01.2024');
        expect(statisticsOptions.dateto).toBe('31.01.20024');
        expect(statisticsOptions.interval).toBe('day');
    });
});

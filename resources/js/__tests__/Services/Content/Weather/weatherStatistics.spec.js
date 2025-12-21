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
});

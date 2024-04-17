import { getPagination, getEmptyPagination } from '@/__tests__/data/paginations';

function getWeather3() {
    const data = [{
        city_id: 4,
        weather_description: "пасмурно",
        main_temp: "8.53",
        main_feels_like: "4.96",
        main_pressure: 997,
        main_humidity: 57,
        visibility: 10000,
        wind_speed: "7.3",
        wind_deg: 239,
        clouds_all: 89,
        created_at: "13:18:14 15.04.2024"
    }, {
        city_id: 4,
        weather_description: "переменная облачность",
        main_temp: "7.63",
        main_feels_like: "4.74",
        main_pressure: 1011,
        main_humidity: 70,
        visibility: 10000,
        wind_speed: "4.73",
        wind_deg: 260,
        clouds_all: 49,
        created_at: "10:33:59 30.03.2024"
    }, {
        city_id: 4,
        weather_description: "ясно",
        main_temp: "2.23",
        main_feels_like: "-1.09",
        main_pressure: 1033,
        main_humidity: 90,
        visibility: 10000,
        wind_speed: "3.35",
        wind_deg: 135,
        clouds_all: 6,
        created_at: "13:00:55 03.03.2024"
    }];

    return getPagination(data, "http://localhost/userlogsweather/4", 1, 10, 3);
}

export const weather3 = getWeather3();

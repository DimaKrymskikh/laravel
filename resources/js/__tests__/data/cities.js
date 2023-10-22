export const cities = [
    {
        id: 2,
        name: "Новосибирск",
        open_weather_id: 1496747,
        timezone_id: 259,
        timezone: { id: 259, name: "Asia/Novosibirsk" }
    }, {
        id: 4,
        name: "Москва",
        open_weather_id: 524901,
        timezone_id: 344,
        timezone: { id: 344, name: "Europe/Moscow" }
    }, {
        id: 6,
        name: "Омск",
        open_weather_id: 1496153,
        timezone_id: null,
        timezone: null
    }
];

export const cities_with_weather = [
    {
        id: 8,
        name: "Барнаул",
        open_weather_id: 1510853,
        timezone_id: null,
        weather_first: {
            city_id: 8,
            weather_description: "ясно",
            main_temp: "5.4",
            main_feels_like: "1.84",
            main_pressure: 1009,
            main_humidity: 60,
            visibility: 10000,
            wind_speed: "5",
            wind_deg: 130,
            clouds_all: 0,
            created_at: "16:43:04 22.10.2023"
        },
        timezone: null
    }, {
        id: 9,
        name: "Владивосток",
        open_weather_id: 2013348,
        timezone_id: 286,
        weather_first: {
            city_id: 9,
            weather_description: "небольшая облачность",
            main_temp: "12.01",
            main_feels_like: "10.62",
            main_pressure: 1017,
            main_humidity: 52,
            visibility: 10000,
            wind_speed: "4.7",
            wind_deg: 306,
            clouds_all: 11,
            created_at: "19:43:06 22.10.2023"
        },
        timezone: {
            id: 286,
            name: "Asia/Vladivostok"
        }
    }, {
        id: 7,
        name: "Томск",
        open_weather_id: 1489425,
        timezone_id: 281,
        weather_first: null,
        timezone: {
            id: 281,
            name: "Asia/Tomsk"
        }
    }
];

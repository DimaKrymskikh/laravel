import { getPagination, getEmptyPagination } from '@/__tests__/data/paginations';

/**
 * Создаёт ответ сервера, содержащий 10 фильмов с пагинацией
 * 
 * @param {boolean} isLogin - Если true, добавляется поле films.data.isAvailable
 * @returns {getFilms10.films}
 */
function getFilms10(isLogin) {
    const data = [
            {
                id: 41,
                title: "Arsenic Independence",
                description	: "A Fanciful Documentary of a Mad Cow And a Womanizer who must Find a Dentist in Berlin",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 42,
                title: "Artist Coldblooded",
                description	: "A Stunning Reflection of a Robot And a Moose who must Challenge a Woman in California",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 43,
                title: "Atlantis Cause",
                description	: "A Thrilling Yarn of a Feminist And a Hunter who must Fight a Technical Writer in A Shark Tank",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 44,
                title: "Attacks Hate",
                description	: "A Fast-Paced Panorama of a Technical Writer And a Mad Scientist who must Find a Feminist in An Abandoned Mine Shaft",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 45,
                title: "Attraction Newton",
                description	: "A Astounding Panorama of a Composer And a Frisbee who must Reach a Husband in Ancient Japan",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 46,
                title: "Autumn Crow",
                description	: "A Beautiful Tale of a Dentist And a Mad Cow who must Battle a Moose in The Sahara Desert",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 47,
                title: "Baby Hall",
                description	: "A Boring Character Study of a A Shark And a Girl who must Outrace a Feminist in An Abandoned Mine Shaft",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 48,
                title: "Backlash Undefeated",
                description	: "A Stunning Character Study of a Mad Scientist And a Mad Cow who must Kill a Car in A Monastery",
                language_id: null,
                language: null
            }, {
                id: 49,
                title: "Badman Dawn",
                description	: "A Emotional Panorama of a Pioneer And a Composer who must Escape a Mad Scientist in A Jet Boat",
                language_id: 1,
                language: { id: 1, name: "English" }
            }, {
                id: 50,
                title: "Baked Cleopatra",
                description	: "A Stunning Drama of a Forensic Psychologist And a Husband who must Overcome a Waitress in A Monastery",
                language_id: 1,
                language: { id: 1, name: "English" }
            }
        ];
    
    const films = getPagination(data, "http://localhost/catalog", 5, 10, 1000);

    if(isLogin) {
        films.data.forEach(function(item, index) {
            item.isAvailable = [2, 4].includes(index) ? true : false;
        });
    }
  
    return films;
}

export const films_10 = getFilms10(false);

export const films_10_user = getFilms10(true);

export const films_0 = getEmptyPagination("http://localhost/catalog", 10);

export const filmCard = {
    id: 986,
    title: "Wonka Sea",
    description: "A Brilliant Saga of a Boat And a Mad Scientist who must Meet a Moose in Ancient India",
    release_year: 2006,
    language_id: 1,
    language: { id: 1, name: "English" },
    actors: [
        {
            id: 27,
            first_name: "Julia",
            last_name: "Mcqueen",
            pivot: { film_id: 986, actor_id: 27 }
        }, {
            id: 60,
            first_name: "Henry",
            last_name: "Berry",
            pivot: { film_id: 986, actor_id: 60 }
        }
    ]
};

/**
 * Создаёт ответ сервера, содержащий 10 фильмов с пагинацией
 * 
 * @param {boolean} isLogin - Если true, добавляется поле films.data.isAvailable
 * @returns {getFilms10.films}
 */
function getFilms10(isLogin) {
    const films = {
        current_page: 5,
        per_page: 10,
        from: 41,
        to: 50,
        total: 1000,
        path: "http://localhost/catalog",
        first_page_url: "http://localhost/catalog?number=10&page=1",
        prev_page_url: "http://localhost/catalog?number=10&page=4",
        next_page_url: "http://localhost/catalog?number=10&page=6",
        last_page_url: "http://localhost/catalog?number=10&page=100",
        data: [
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
                language_id: 1,
                language: { id: 1, name: "English" }
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
        ],
        links: [
            {
                url: "http://localhost/catalog?number=10&page=4",
                label: "&laquo; Previous",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=1",
                label: "1",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=2",
                label: "2",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=3",
                label: "3",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=4",
                label: "4",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=5",
                label: "5",
                active: true
            }, {
                url: "http://localhost/catalog?number=10&page=6",
                label: "6",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=7",
                label: "7",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=8",
                label: "8",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=9",
                label: "9",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=10",
                label: "10",
                active: false
            }, {
                url: null,
                label: "...",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=99",
                label: "99",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=100",
                label: "100",
                active: false
            }, {
                url: "http://localhost/catalog?number=10&page=6",
                label: "Next &raquo;",
                active: false
            }
        ]
    };
    
    if(isLogin) {
        films.data.forEach(function(item, index) {
            item.isAvailable = [2, 4].includes(index) ? true : false;
        });
    }
    
    return films;
}

export const films_10 = getFilms10(false);

export const films_10_user = getFilms10(true);

export const films_0 = {
    current_page: 1,
    per_page: 10,
    from: null,
    to: null,
    total: 0,
    path: "http://localhost/catalog",
    first_page_url: "http://localhost/catalog?number=10&description=qqqq&page=1",
    last_page_url: "http://localhost/catalog?number=10&description=qqqq&page=1",
    data: [],
    links: [
        {
            url: null,
            label: "&laquo; Previous",
            active: false
        }, {
            url: "http://localhost/catalog?number=10&description=qqqq&page=1",
            label: "1",
            active: true
        }, {
            url: null,
            label: "Next &raquo;",
            active: false
        }
    ]
};

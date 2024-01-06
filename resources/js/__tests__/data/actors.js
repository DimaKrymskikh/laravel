export const actors = {
    current_page: 2,
    per_page: 10,
    from: 1,
    to: 3,
    total: 3,
    data: [
        {
            id: 1,
            first_name: 'Penelope',
            last_name: 'Guiness'
        }, {
            id: 2,
            first_name: 'Nick',
            last_name: 'Wahlberg'
        }, {
            id: 3,
            first_name: 'Ed',
            last_name: 'Chase'
        }
    ],
    links: [
        {
            url: null,
            label: "&laquo; Previous",
            active: false
        }, {
            url: "http://localhost/admin/actors?number=20&page=1",
            label: "1",
            active: true
        }, {
            url: null,
            label: "Next &raquo;",
            active: false
        }
    ]
};

export const actors_0 = {
    current_page: 1,
    per_page: 10,
    from: null,
    to: null,
    total: 0,
    path: "http://localhost/admin/actors",
    first_page_url: "http://localhost/admin/actors?number=20&page=1",
    last_page_url: "http://localhost/admin/actors?number=20&page=1",
    data: [],
    links: [
        {
            url: null,
            label: "&laquo; Previous",
            active: false
        }, {
            url: "http://localhost/admin/actors?number=20&page=1",
            label: "1",
            active: true
        }, {
            url: null,
            label: "Next &raquo;",
            active: false
        }
    ]
};

export const json_film_actors = {
    actors: [
        { id: 55, first_name: "Fay", last_name: "Kilmer" },
        { id: 96, first_name: "Gene", last_name: "Willis" },
        { id: 138, first_name: "Lucille", last_name: "Dee" },
        { id: 110, first_name: "Susan", last_name: "Davis" }
    ]
};

export const json_film_actors_0 = {
    actors: []
};

export const json_free_actors = [
    { id: 165, first_name: "Al", last_name: "Garland" },
    { id: 34, first_name: "Audrey", last_name: "Olivier" },
    { id: 63, first_name: "Cameron", last_name: "Wray" },
    { id: 10, first_name: "Christian", last_name: "Gable" },
    { id: 129, first_name: "Daryl", last_name: "Crawford" },
    { id: 55, first_name: "Fay", last_name: "Kilmer" },
    { id: 177, first_name: "Gene", last_name: "Mckellen" }
];

export const json_free_actors_0 = [];

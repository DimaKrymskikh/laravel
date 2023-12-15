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

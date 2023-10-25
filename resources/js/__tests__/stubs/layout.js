export const AuthAccountLayoutStub = {
    name: 'AccountLayoutStub',
    props: {
            user: Object | null,
            errors: Object,
            linksList: Array
        },
    template: '<slot />'
};

export const AdminLayoutStub = {
    name: 'AdminLayoutStub',
    props: {
            errors: Object
        },
    template: '<slot />'
};

export const AuthLayoutStub = {
    name: 'AuthLayoutStub',
    props: {
            user: Object | null,
            errors: Object
        },
    template: '<slot />'
};

export const GuestLayoutStub = {
    name: 'GuestLayoutStub',
    template: '<slot />'
};

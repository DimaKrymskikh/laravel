import { reactive } from 'vue';

export const film = reactive({
    id: 0,
    title: '',
    description: '',
    field: '',
    fieldValue: '',
    
    isShowAddFilmModal: false,
    showAddFilmModal() {
        this.isShowAddFilmModal = true;
        this.title = '';
        this.description = '';
    },
    hideAddFilmModal() {
        this.isShowAddFilmModal = false;
    },
    
    isShowUpdateFilmModal: false,
    showUpdateFilmModal() {
        this.isShowUpdateFilmModal = true;
    },
    hideUpdateFilmModal() {
        this.isShowUpdateFilmModal = false;
    },
    
    isShowUpdateFilmActorsModal: false,
    showUpdateFilmActorsModal() {
        this.isShowUpdateFilmActorsModal = true;
    },
    hideUpdateFilmActorsModal() {
        this.isShowUpdateFilmActorsModal = false;
    },

    isShowUpdateFilmLanguageModal: false,
    showUpdateFilmLanguageModal() {
        this.isShowUpdateFilmLanguageModal = true;
    },
    hideUpdateFilmLanguageModal() {
        this.isShowUpdateFilmLanguageModal = false;
    },
    
    isShowRemoveFilmModal: false,
    showRemoveFilmModal() {
        this.isShowRemoveFilmModal = true;
    },
    hideRemoveFilmModal() {
        this.isShowRemoveFilmModal = false;
    }
});

export const removeActor = reactive({
    id: 0,
    firstName: '',
    lastName: '',
    
    isRemoveActorFromFilmModal: false,
    showRemoveActorFromFilmModal() {
        this.isRemoveActorFromFilmModal = true;
    },
    hideRemoveActorFromFilmModal() {
        this.isRemoveActorFromFilmModal = false;
    }
});

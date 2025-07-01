import { reactive } from 'vue';

export const actor = reactive({
    id: 0,
    firstName: '',
    lastName: '',
    
    isShowAddActorModal: false,
    showAddActorModal() {
        this.isShowAddActorModal = true;
        // Модальное окно добавления актёра всегда открывается с пустыми полями
        this.firstName = '';
        this.lastName = '';
    },
    hideAddActorModal() {
        this.isShowAddActorModal = false;
    },
    
    isShowUpdateActorModal: false,
    showUpdateActorModal() {
        this.isShowUpdateActorModal = true;
    },
    hideUpdateActorModal() {
        this.isShowUpdateActorModal = false;
    },
    
    isShowRemoveActorModal: false,
    showRemoveActorModal() {
        this.isShowRemoveActorModal = true;
    },
    hideRemoveActorModal() {
        this.isShowRemoveActorModal = false;
    }
});

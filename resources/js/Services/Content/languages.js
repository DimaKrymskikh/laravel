import { reactive } from 'vue';

export const language = reactive({
    id: 0,
    name: '',
    
    isShowAddLanguageModal: false,
    showAddLanguageModal() {
        this.isShowAddLanguageModal = true;
        this.name = '';
    },
    hideAddLanguageModal() {
        this.isShowAddLanguageModal = false;
    },
    
    isShowUpdateLanguageModal: false,
    showUpdateLanguageModal() {
        this.isShowUpdateLanguageModal = true;
    },
    hideUpdateLanguageModal() {
        this.isShowUpdateLanguageModal = false;
    },
    
    isShowRemoveLanguageModal: false,
    showRemoveLanguageModal() {
        this.isShowRemoveLanguageModal = true;
    },
    hideRemoveLanguageModal() {
        this.isShowRemoveLanguageModal = false;
    }
});

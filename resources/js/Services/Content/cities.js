import { reactive } from 'vue';

export const city = reactive({
    id: 0,
    name: '',
    openWeatherId: 0,
    timeZone: '',
    
    isShowAddCityModal: false,
    showAddCityModal() {
        this.isShowAddCityModal = true;
        this.name = '';
        this.openWeatherId = 0;
    },
    hideAddCityModal() {
        this.isShowAddCityModal = false;
    },
    
    isShowUpdateCityModal: false,
    showUpdateCityModal() {
        this.isShowUpdateCityModal = true;
    },
    hideUpdateCityModal() {
        this.isShowUpdateCityModal = false;
    },
    
    isShowRemoveCityModal: false,
    showRemoveCityModal() {
        this.isShowRemoveCityModal = true;
    },
    hideRemoveCityModal() {
        this.isShowRemoveCityModal = false;
    },
    
    isShowUpdateTimeZoneModal: false,
    showUpdateTimeZoneModal() {
        this.isShowUpdateTimeZoneModal = true;
    },
    hideUpdateTimeZoneModal() {
        this.isShowUpdateTimeZoneModal = false;
    }
});

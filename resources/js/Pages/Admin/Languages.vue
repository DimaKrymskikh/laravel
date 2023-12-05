<script setup>
import { ref, reactive } from 'vue';
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddLanguageBlock from '@/Components/Pages/Admin/Languages/AddLanguageBlock.vue';
import RemoveLanguageModal from '@/Components/Modal/Request/Languages/RemoveLanguageModal.vue';
import UpdateLanguageModal from '@/Components/Modal/Request/Languages/UpdateLanguageModal.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

defineProps({
    languages: Array | null,
    errors: Object
});

const titlePage = 'Языки';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];
    
const updateLanguage = reactive({
    id: 0,
    name: ''
});
const isShowUpdateLanguageModal = ref(false);
const hideUpdateLanguageModal = function() {
    isShowUpdateLanguageModal.value = false;
};
    
const removeLanguage = reactive({
    id: 0,
    name: ''
});
const isShowRemoveLanguageModal = ref(false);
const hideRemoveLanguageModal = function() {
    isShowRemoveLanguageModal.value = false;
};

const handlerTableChange = function(e) {
    let td = e.target.closest('td');
    
    if (td && td.classList.contains('update-language')) {
        updateLanguage.id = td.getAttribute('data-language_id');
        updateLanguage.name = td.getAttribute('data-language_name');
        isShowUpdateLanguageModal.value = true;
    }
    
    if (td && td.classList.contains('remove-language')) {
        removeLanguage.id = td.getAttribute('data-language_id');
        removeLanguage.name = td.getAttribute('data-language_name');
        isShowRemoveLanguageModal.value = true;
    }
};
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-start mb-4">
            <AddLanguageBlock />
        </div>
        
        <table @click="handlerTableChange" v-if="languages.length">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Язык</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(language, index) in languages" class="hover:bg-green-300">
                    <td class="font-sans">{{ index + 1 }}</td>
                    <td>{{ language.name }}</td>
                    <td class="update-language" :data-language_id="language.id" :data-language_name="language.name">
                        <PencilSvg
                            title="Редактировать язык"
                        />
                    </td>
                    <td class="remove-language" :data-language_id="language.id" :data-language_name="language.name">
                        <TrashSvg
                            title="Удалить язык"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-else>
            Ещё ни один язык не добавлен
        </div>
        
        <UpdateLanguageModal
            :updateLanguage="updateLanguage"
            :hideUpdateLanguageModal="hideUpdateLanguageModal"
            v-if="isShowUpdateLanguageModal"
        />
        
        <RemoveLanguageModal
            :removeLanguage="removeLanguage"
            :hideRemoveLanguageModal="hideRemoveLanguageModal"
            v-if="isShowRemoveLanguageModal"
        />
    </AdminLayout>
</template>

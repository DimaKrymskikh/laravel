import { ref } from 'vue';

export const getPaginationOptions = function () {
    // Активная страница
    // По умолчанию, первая страница является активной
    const page = ref(1);
    // Число элементов на странице
    // По умолчанию, на одной странице должно быть 20 элементов
    const perPage = ref(20);
    
    return { page, perPage };
};

export const paginationOptions = {
    // Активная страница
    // По умолчанию, первая страница является активной
    page: 1,
    // Число элементов на странице
    // По умолчанию, на одной странице должно быть 20 элементов
    perPage: 20
};

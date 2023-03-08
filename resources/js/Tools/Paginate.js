/**
 * Если на последней странице удаляется единственный элемент, то возвращается номер предыдущей страницы
 * @param {Object} items
 * @returns {Number}
 */
export const getPageAfterRemoveItem = function(items) {
    return items.from === items.to ? items.current_page - 1 : items.current_page;
};

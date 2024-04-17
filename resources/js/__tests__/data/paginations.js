function isPaginationButton(page, currentPage, lastPage) {
    return page === 1 ||
           page === 2 ||
           (currentPage <= 7 && page <= 10) ||
           (page >= currentPage - 3 && page <= currentPage + 3) ||
           (currentPage >= lastPage - 6 && page >= lastPage - 9) ||
           page === lastPage - 1 ||
           page === lastPage
        ? true : false;
}

function getLinks(path, currentPage, perPage, lastPage) {
    let links = [];
    
    links.push({
            url: currentPage !== 1 ? `${path}?number=${perPage}&page=${currentPage - 1}` : null,
            label: "&laquo; Previous",
            active: false
        });
            
    for(let i = 1; i <= lastPage; i++) {
        let isButton = isPaginationButton(i, currentPage, lastPage);
        if(isButton) {
            links.push({
                    url: currentPage !== 1 ? `${path}?number=${perPage}&page=${i}` : null,
                    label: `${i}`,
                    active: !!(i === currentPage)
                });
        }
        
        if(!isButton && (i === currentPage + 4 || i === currentPage - 4)) {
            links.push({
                    url: null,
                    label: "...",
                    active: false
                });
        }
    }
    
    links.push({
            url: currentPage !== lastPage ? `${path}?number=${perPage}&page=${currentPage + 1}` : null,
            label: "Next &raquo;",
            active: false
        });
            
    return links;
}

function getEmptyLinks(path, perPage) {
    
    return [
            {
                url: null,
                label: "&laquo; Previous",
                active: false
            }, {
                url: `${path}?number=${perPage}&page=1`,
                label: "1",
                active: true
            }, {
                url: null,
                label: "Next &raquo;",
                active: false
            }
        ];
}

export function getPagination(data, path, currentPage, perPage, total) {
    const one = total % perPage ? 1 : 0;
    const lastPage = Math.floor(total / perPage) + one;
    
    return {
        current_page: currentPage,
        data,
        first_page_url: `${path}?number=${perPage}&page=1`,
        from: (currentPage - 1) * perPage + 1,
        last_page: lastPage,
        last_page_url: `${path}?number=${perPage}&page=${lastPage}`,
        links: getLinks(path, currentPage, perPage, lastPage),
        next_page_url: currentPage < lastPage ? `${path}?number=${perPage}&page=${currentPage + 1}` : null,
        path,
        per_page: perPage,
        prev_page_url: currentPage > 1 ? `${path}?number=${perPage}&page=${currentPage - 1}` : null,
        to: currentPage < lastPage ? currentPage * perPage : total - (lastPage - 1) * perPage,
        total
    };
}

export function getEmptyPagination(path, perPage) {
    return {
        current_page: 1,
        data: [],
        first_page_url: `${path}?number=${perPage}&page=1`,
        from: null,
        last_page: 1,
        last_page_url: `${path}?number=${perPage}&page=1`,
        links: getEmptyLinks(path, perPage),
        next_page_url: null,
        path,
        per_page: perPage,
        prev_page_url: null,
        to: null,
        total: 0
    };
}

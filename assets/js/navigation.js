const navigationElement = document.getElementById('page-navigation');

function createNavigationButtons(currentPageId) {
    const currentIndex = pagesArray.findIndex(page => page.id === currentPageId);

    if (currentIndex === -1) return;

    const prevPage = currentIndex > 0 ? pagesArray[currentIndex - 1] : null;
    const nextPage = currentIndex < pagesArray.length - 1 ? pagesArray[currentIndex + 1] : null;

    let navHTML = '';

    if (prevPage) {
        navHTML += `
            <button class="nav-button prev" data-page-id="${prevPage.id}">
                <i class="ri-arrow-left-line"></i>
                <span>
                    <small class="nav-info">Previous</small>
                    ${prevPage.title}
                </span>
            </button>
        `;
    } else {
        navHTML += `
            <button class="nav-button prev disabled" disabled>
                <i class="ri-arrow-left-line"></i>
                <span>
                    <small class="nav-info">Previous</small>
                    No Previous Page
                </span>
            </button>
        `;
    }

    if (nextPage) {
        navHTML += `
            <button class="nav-button next" data-page-id="${nextPage.id}">
                <span>
                    <small class="nav-info">Next</small>
                    ${nextPage.title}
                </span>
                <i class="ri-arrow-right-line"></i>
            </button>
        `;
    } else {
        navHTML += `
            <button class="nav-button next disabled" disabled>
                <span>
                    <small class="nav-info">Next</small>
                    No Next Page
                </span>
                <i class="ri-arrow-right-line"></i>
            </button>
        `;
    }

    navigationElement.innerHTML = navHTML;

    const prevButton = navigationElement.querySelector('.prev:not(.disabled)');
    const nextButton = navigationElement.querySelector('.next:not(.disabled)');

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            loadPage(prevButton.dataset.pageId);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            loadPage(nextButton.dataset.pageId);
        });
    }
}

document.addEventListener('keydown', (e) => {
    const currentIndex = pagesArray.findIndex(page => page.id === currentPage);

    if (currentIndex === -1) return;

    if (e.key === 'ArrowLeft' && currentIndex > 0) {
        loadPage(pagesArray[currentIndex - 1].id);
    }

    if (e.key === 'ArrowRight' && currentIndex < pagesArray.length - 1) {
        loadPage(pagesArray[currentIndex + 1].id);
    }
});

window.addEventListener('popstate', (event) => {
    if (event.state && event.state.pageId) {
        loadPage(event.state.pageId);
    } else {
        const defaultPageId = config.defaultPage || (docsStructure && docsStructure.pages[0] && docsStructure.pages[0].id);
        if (defaultPageId) {
            loadPage(defaultPageId);
        }
    }
});
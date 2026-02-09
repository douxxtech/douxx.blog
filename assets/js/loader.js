const config = {
    defaultPage: '1-intro',
    markdownPath: './articles/'
};

const sidebarMenu = document.getElementById('sidebar-menu');
const contentElement = document.getElementById('content');
const navigationElement = document.getElementById('page-navigation');
const projectNameElement = document.getElementById('project-name');
const projectNameMobileElement = document.getElementById('project-name-mobile');
const menuButton = document.querySelector('.menu-button');
const sidebar = document.querySelector('.sidebar');
const searchInput = document.querySelector('.search-input');

let docsStructure = null;
let currentPage = null;
let pages = {};
let pagesArray = [];

const renderer = new marked.Renderer();
const originalBlockquote = renderer.blockquote;

renderer.blockquote = function (quote) {
    const calloutMatch = quote.match(/<p>\[!(INFO|NOTE|TIP|IMPORTANT|WARNING|CAUTION|DANGER)\](.*?)(?:<\/p>)?/i);

    if (calloutMatch) {
        const type = calloutMatch[1].toLowerCase();
        let content = quote.replace(/<p>\[!.*?\](.*?)(?:<\/p>)?/i, '$1');

        let icon = '';
        switch (type) {
            case 'info':
            case 'note':
                icon = '<i class="ri-information-line"></i>';
                break;
            case 'tip':
                icon = '<i class="ri-lightbulb-line"></i>';
                break;
            case 'important':
                icon = '<i class="ri-alert-line"></i>';
                break;
            case 'warning':
            case 'caution':
                icon = '<i class="ri-error-warning-line"></i>';
                break;
            case 'danger':
                icon = '<i class="ri-close-circle-line"></i>';
                break;
            default:
                icon = '<i class="ri-information-line"></i>';
        }

        return `<div class="callout callout-${type}">${icon}<div class="callout-content">${content}</div></div>`;
    }

    return originalBlockquote.call(this, quote);
};

marked.setOptions({
    renderer: renderer,
    highlight: function (code, lang) {
        if (lang && hljs.getLanguage(lang)) {
            return hljs.highlight(code, { language: lang }).value;
        }
        return hljs.highlightAuto(code).value;
    },
    breaks: true,
    gfm: true
});

menuButton.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

searchInput.addEventListener('input', (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const menuItems = document.querySelectorAll('.menu-item');

    menuItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm) || searchTerm === '') {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

async function loadDocStructure() {
    try {
        const response = await fetch(config.markdownPath);
        if (!response.ok) {
            throw new Error(`Failed to load documentation structure: ${response.status}`);
        }

        docsStructure = await response.json();
        projectNameElement.textContent = docsStructure.title || 'Blog';
        projectNameMobileElement.textContent = docsStructure.title || 'Blog';
        document.title = docsStructure.title || 'Blog';

        pagesArray = docsStructure.pages;

        buildSidebar(docsStructure);

    } catch (error) {
        console.error('Error loading documentation structure:', error);
        contentElement.innerHTML = `
            <h1>Error Loading Documentation</h1>
            <div class="error">
                <p>Failed to load the documentation structure. Please check your PHP script.</p>
                <pre>${error.message}</pre>
            </div>
        `;
    }
}

function buildSidebar(structure) {
    sidebarMenu.innerHTML = '';

    structure.pages.forEach(page => {
        pages[page.id] = page;
    });

    let currentCategory = null;

    structure.pages.forEach(page => {
        if (page.category && page.category !== currentCategory) {
            currentCategory = page.category;
            const categoryEl = document.createElement('div');
            categoryEl.className = 'menu-category';
            categoryEl.textContent = currentCategory;
            sidebarMenu.appendChild(categoryEl);
        }

        const menuItem = document.createElement('div');
        menuItem.className = 'menu-item';
        
        if (page.date) {
            const dateEl = document.createElement('span');
            dateEl.className = 'menu-item-date';
            dateEl.textContent = formatDate(page.date);
            
            const titleEl = document.createElement('span');
            titleEl.className = 'menu-item-title';
            titleEl.textContent = page.title;
            
            menuItem.appendChild(dateEl);
            menuItem.appendChild(titleEl);
        } else {
            menuItem.textContent = page.title;
        }
        
        menuItem.dataset.pageId = page.id;
        menuItem.addEventListener('click', () => loadPage(page.id));

        sidebarMenu.appendChild(menuItem);
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function isURL(str) {
    try {
        new URL(str);
        return true;
    } catch (_) {
        return false;
    }
}

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

async function loadPage(pageId) {
    if (!pages[pageId]) {
        console.error(`Page not found: ${pageId}`);
        contentElement.innerHTML = '<h1>Page Not Found</h1><p>The requested page does not exist.</p>';
        return;
    }

    const page = pages[pageId];
    currentPage = pageId;

    document.querySelectorAll('.menu-item').forEach(item => {
        item.classList.remove('active');
        if (item.dataset.pageId === pageId) {
            item.classList.add('active');
        }
    });

    if (window.innerWidth <= 768) {
        sidebar.classList.remove('active');
    }

    contentElement.innerHTML = '<div class="loading">Loading page...</div>';

    try {
        let markdown;

        const filePath = page.file || `${pageId}.md`;

        if (isURL(filePath)) {
            const response = await fetch(filePath);

            if (!response.ok) {
                throw new Error(`Failed to load markdown from URL: ${response.status}`);
            }

            markdown = await response.text();
        } else {
            const markdownPath = `${config.markdownPath}${filePath}`;
            const response = await fetch(markdownPath);

            if (!response.ok) {
                throw new Error(`Failed to load markdown file: ${response.status}`);
            }

            markdown = await response.text();
        }

        contentElement.innerHTML = marked.parse(markdown);

        // Add eye icon for unique views
        if (page.trackurl) {
            fetchUniqueViews(page.trackurl);
        }

        history.pushState({ pageId }, page.title, `?p=${pageId}`);
        document.title = `${page.title} - ${docsStructure.title || 'Documentation'}`;

        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightElement(block);
        });

        handleRelativeLinks();

        createNavigationButtons(pageId);
        handleExternalLinks();
        executeScripts(contentElement);

    } catch (error) {
        console.error(`Error loading page ${pageId}:`, error);
        contentElement.innerHTML = `
            <h1>Error Loading Page</h1>
            <div class="error">
                <p>Failed to load the page content.</p>
                <pre>${error.message}</pre>
            </div>
        `;

        navigationElement.innerHTML = '';
    }
}
async function fetchUniqueViews(trackurl) {
    if (localStorage.getItem('dpipTrack') === 'false') return;

    try {
        const response = await fetch(trackurl);
        if (!response.ok) {
            throw new Error(`Failed to fetch unique views: ${response.status}`);
        }

        const data = await response.json();
        const uniqueViews = data.unique_views || 0;

        let eyeIcon = contentElement.querySelector('.view-count');

        if (eyeIcon) {
            eyeIcon.innerHTML = `<i class="ri-eye-line"></i> ${uniqueViews}`;
        } else {
            eyeIcon = document.createElement('div');
            eyeIcon.className = 'view-count';
            eyeIcon.innerHTML = `<i class="ri-eye-line"></i> ${uniqueViews}`;
            contentElement.appendChild(eyeIcon);
        }
    } catch (error) {
        console.error('Error fetching unique views:', error);
    }
}

function handleRelativeLinks() {
    const links = contentElement.querySelectorAll('a');

    links.forEach(link => {
        const href = link.getAttribute('href');

        if (!href || href.startsWith('#') || href.startsWith('http://') || href.startsWith('https://')) {
            return;
        }

        if (href.endsWith('.md')) {
            const pageId = href.replace('.md', '');

            if (pages[pageId]) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    loadPage(pageId);
                });
            }
        }
    });
}


function executeScripts(container) {
    const scripts = container.querySelectorAll('script');

    scripts.forEach(oldScript => {
        const newScript = document.createElement('script');

        Array.from(oldScript.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });

        newScript.textContent = oldScript.textContent;

        oldScript.replaceWith(newScript);
    });
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

function loadPageFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('p');

    if (pageParam && pages[pageParam]) {
        loadPage(pageParam);
    }
}

async function init() {
    await loadDocStructure();

    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('p');

    if (pageParam && pages[pageParam]) {
        loadPage(pageParam);
    } else {
        const defaultPageId = config.defaultPage || (docsStructure.pages[0] && docsStructure.pages[0].id);
        if (defaultPageId) {
            loadPage(defaultPageId);
        }
    }
}

document.addEventListener('DOMContentLoaded', init);

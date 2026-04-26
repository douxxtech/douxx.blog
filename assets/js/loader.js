const contentElement = document.getElementById('content');

function isURL(str) {
    try {
        new URL(str);
        return true;
    } catch (_) {
        return false;
    }
}

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

        pagesArray = [
            ...pagesArray.filter(p => p.id === config.defaultPage),
            ...pagesArray
                .filter(p => p.id !== config.defaultPage)
                .sort((a, b) => new Date(b.date) - new Date(a.date))
        ];
        docsStructure.pages = pagesArray;

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

async function loadPage(pageId) {
    window.scrollTo(0, 0);

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
        initProgressBar();
        initGiscus();
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
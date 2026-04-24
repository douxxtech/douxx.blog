const sidebarMenu = document.getElementById('sidebar-menu');
const projectNameElement = document.getElementById('project-name');
const projectNameMobileElement = document.getElementById('project-name-mobile');
const menuButton = document.querySelector('.menu-button');
const sidebar = document.querySelector('.sidebar');
const searchInput = document.querySelector('.search-input');

if (window.innerWidth <= 768) sidebar.classList.add('hidden');

menuButton.addEventListener('click', () => {
    sidebar.classList.toggle('hidden');
    document.querySelector('.main-content').classList.toggle('expanded');
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

function buildSidebar(structure) {
    sidebarMenu.innerHTML = '';

    structure.pages.forEach(page => {
        pages[page.id] = page;
    });

    structure.pages.forEach(page => {
        const menuItem = document.createElement('div');
        menuItem.className = 'menu-item';

        const titleEl = document.createElement('span');
        titleEl.className = 'menu-item-title';
        titleEl.textContent = page.title;
        menuItem.appendChild(titleEl);

        // date + category as a small subtitle line
        const metaEl = document.createElement('span');
        metaEl.className = 'menu-item-date';
        const parts = [];
        if (page.date) parts.push(formatDate(page.date));
        if (page.category) parts.push(page.category);
        metaEl.textContent = parts.join(' · ');
        menuItem.appendChild(metaEl);

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
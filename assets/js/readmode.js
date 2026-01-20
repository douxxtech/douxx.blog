const readModeToggle = document.getElementById('readModeToggle');
const body = document.body;

if (localStorage.getItem('readMode') === 'enabled') {
    body.classList.add('read-mode');
    readModeToggle.classList.add('active');
}

readModeToggle.addEventListener('click', () => {
    body.classList.toggle('read-mode');
    readModeToggle.classList.toggle('active');
    
    if (body.classList.contains('read-mode')) {
        localStorage.setItem('readMode', 'enabled');
    } else {
        localStorage.removeItem('readMode');
    }
});
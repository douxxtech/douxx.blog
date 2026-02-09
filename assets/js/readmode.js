const readModeToggle = document.getElementById('readModeToggle');
const dyslexiaToggle = document.getElementById('dyslexiaToggle');
const body = document.body;

if (localStorage.getItem('readMode') === 'enabled') {
    body.classList.add('read-mode');
    readModeToggle.classList.add('active');
}

if (localStorage.getItem('dyslexiaMode') === 'enabled') {
    body.classList.add('dyslexia-mode');
    dyslexiaToggle.classList.add('active');
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

dyslexiaToggle.addEventListener('click', () => {
    body.classList.toggle('dyslexia-mode');
    dyslexiaToggle.classList.toggle('active');
    
    if (body.classList.contains('dyslexia-mode')) {
        localStorage.setItem('dyslexiaMode', 'enabled');
    } else {
        localStorage.removeItem('dyslexiaMode');
    }
});
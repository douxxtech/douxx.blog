const dyslexiaToggle = document.getElementById('dyslexiaToggle');
const lightModeToggle = document.getElementById('lightModeToggle');
const body = document.body;

const lightMode = localStorage.getItem('lightMode');

if (lightMode) {
    if (lightMode === 'enabled') setToLight();
} else {
    // check for browser preference and set accordingly
    if (window.matchMedia('(prefers-color-scheme: light)').matches) {
        setToLight();
        localStorage.setItem('lightMode', 'enabled');
    }
}

function setToLight() {
    body.classList.add('light-mode');
    lightModeToggle.classList.add('active');
}

if (localStorage.getItem('dyslexiaMode') === 'enabled') {
    body.classList.add('dyslexia-mode');
    dyslexiaToggle.classList.add('active');
}

dyslexiaToggle.addEventListener('click', () => {
    body.classList.toggle('dyslexia-mode');
    dyslexiaToggle.classList.toggle('active');

    if (body.classList.contains('dyslexia-mode')) {
        localStorage.setItem('dyslexiaMode', 'enabled');
    } else {
        localStorage.removeItem('dyslexiaMode');
    }
});

lightModeToggle.addEventListener('click', () => {
    body.classList.toggle('light-mode');
    lightModeToggle.classList.toggle('active');

    if (body.classList.contains('light-mode')) {
        localStorage.setItem('lightMode', 'enabled');
    } else {
        localStorage.removeItem('lightMode');
    }
});
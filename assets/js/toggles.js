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
    syncCodeTheme();
}

function syncCodeTheme() {
    const isLight = body.classList.contains('light-mode');
    document.getElementById('hljs-theme').disabled = isLight;
    document.getElementById('hljs-theme-light').disabled = !isLight;

    const giscus = document.querySelector('iframe.giscus-frame');
    if (giscus) {
        giscus.contentWindow.postMessage({
            giscus: {
                setConfig: {
                    theme: body.classList.contains('light-mode')
                        ? 'https://douxx.blog/assets/css/giscus-light.css?12'
                        : 'https://douxx.blog/assets/css/giscus.css?12'
                }
            }
        }, 'https://giscus.app');
    }
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

    syncCodeTheme();
});
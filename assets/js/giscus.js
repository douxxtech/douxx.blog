function initGiscus() {
    const giscus = document.getElementById('giscus-container');
    const existingFrame = document.querySelector('iframe.giscus-frame');
    const isLight = document.body.classList.contains('light-mode');
    const theme = isLight ? 'https://douxx.blog/assets/css/giscus-light.css?12' : 'https://douxx.blog/assets/css/giscus.css?12';

    if (existingFrame) {
        existingFrame.contentWindow.postMessage({
            giscus: {
                setConfig: {
                    term: window.location.href,
                    theme: theme
                }
            }
        }, 'https://giscus.app');
        return;
    }

    if (giscus) giscus.innerHTML = '';

    const script = document.createElement('script');
    script.src = 'https://giscus.app/client.js';
    script.setAttribute('data-repo', 'douxxtech/douxx.blog');
    script.setAttribute('data-repo-id', 'R_kgDOODxnVA');
    script.setAttribute('data-category', 'General');
    script.setAttribute('data-category-id', 'DIC_kwDOODxnVM4C7uZI');
    script.setAttribute('data-mapping', 'url');
    script.setAttribute('data-strict', '0');
    script.setAttribute('data-reactions-enabled', '1');
    script.setAttribute('data-emit-metadata', '0');
    script.setAttribute('data-input-position', 'top');
    script.setAttribute('data-theme', theme);
    script.setAttribute('data-lang', 'en');
    script.crossOrigin = 'anonymous';
    script.async = true;

    giscus.appendChild(script);
}
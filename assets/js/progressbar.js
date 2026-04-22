const progressBar = document.getElementById('progress-bar');

function initProgressBar() {
    window.removeEventListener('scroll', updateProgressBar);  // reset eventual pb

    // check if content is taller than the viewport
    const contentHeight = contentElement.scrollHeight;
    const viewportHeight = window.innerHeight;

    if (contentHeight <= viewportHeight) {
        progressBar.style.opacity = '0';
        window.removeEventListener('scroll', updateProgressBar);
        return;
    }

    progressBar.style.opacity = '1';
    updateProgressBar();
    window.addEventListener('scroll', updateProgressBar);
}

function updateProgressBar() {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const progress = (scrollTop / docHeight) * 100;
    progressBar.style.width = `${progress}%`;
}
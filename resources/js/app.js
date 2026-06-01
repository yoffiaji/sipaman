import './bootstrap';

if ('scrollRestoration' in history) {
    history.scrollRestoration = 'auto';
}

document.addEventListener('DOMContentLoaded', () => {
    initAutoResizeTextareas();
    clearLegacyAdminScrollCache();
});

function initAutoResizeTextareas() {
    document.querySelectorAll('[data-auto-resize]').forEach((textarea) => {
        const resize = () => {
            textarea.style.height = 'auto';
            textarea.style.height = `${textarea.scrollHeight}px`;
        };

        textarea.classList.add('scrollbar-none');
        textarea.style.overflow = 'hidden';

        resize();

        textarea.addEventListener('input', resize);
    });
}

function clearLegacyAdminScrollCache() {
    Object.keys(localStorage)
        .filter((key) => key.startsWith('sipaman.admin.page.scrollY:') || key === 'sipaman.admin.sidebar.scrollTop')
        .forEach((key) => localStorage.removeItem(key));
}
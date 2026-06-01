import './bootstrap';

if ('scrollRestoration' in history) {
    history.scrollRestoration = 'auto';
}

document.addEventListener('DOMContentLoaded', () => {
    initAutoResizeTextareas();
    initAdminSidebarScroll();
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
    try {
        Object.keys(localStorage)
            .filter((key) => key.startsWith('sipaman.admin.page.scrollY:') || key === 'sipaman.admin.sidebar.scrollTop')
            .forEach((key) => localStorage.removeItem(key));
    } catch {
        // Storage can be unavailable in restricted browser modes.
    }
}

function initAdminSidebarScroll() {
    const sidebar = document.getElementById('admin-sidebar-scroll');

    if (!sidebar) {
        return;
    }

    const storageKey = 'sipaman.admin.sidebar.scrollTop';
    const activeItem = sidebar.querySelector('[data-sidebar-active="true"]');
    const readStoredScrollTop = () => {
        try {
            return Number.parseInt(sessionStorage.getItem(storageKey) ?? '', 10);
        } catch {
            return NaN;
        }
    };
    const writeStoredScrollTop = () => {
        try {
            sessionStorage.setItem(storageKey, String(sidebar.scrollTop));
        } catch {
            // Keep sidebar usable even when storage is unavailable.
        }
    };
    const storedScrollTop = readStoredScrollTop();

    if (Number.isFinite(storedScrollTop)) {
        sidebar.scrollTop = Math.max(storedScrollTop, 0);
    } else if (activeItem) {
        activeItem.scrollIntoView({ block: 'nearest' });
    }

    sidebar.style.visibility = 'visible';

    let frame = null;
    const rememberScrollTop = () => {
        if (frame !== null) {
            return;
        }

        frame = requestAnimationFrame(() => {
            writeStoredScrollTop();
            frame = null;
        });
    };

    sidebar.addEventListener('scroll', rememberScrollTop, { passive: true });

    sidebar.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', () => {
            writeStoredScrollTop();
        });
    });
}

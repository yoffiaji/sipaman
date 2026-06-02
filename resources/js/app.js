import './bootstrap';

if ('scrollRestoration' in history) {
    history.scrollRestoration = 'auto';
}

document.addEventListener('DOMContentLoaded', () => {
    initAutoResizeTextareas();
    initButtonUrlFields();
    initConfirmableForms();
    initDialogCloseButtons();
    initPublicNavigation();
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

function initPublicNavigation() {
    initAccountMenus();
    initMobileNavigation();
}

function initButtonUrlFields() {
    document.querySelectorAll('[data-button-url-select]').forEach((select) => {
        const wrapper = select.parentElement?.querySelector('[data-custom-url-field]');

        if (!wrapper) {
            return;
        }

        const toggleCustomUrl = () => {
            wrapper.classList.toggle('hidden', select.value !== 'custom');
        };

        toggleCustomUrl();
        select.addEventListener('change', toggleCustomUrl);
    });
}

function initConfirmableForms() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm');

            if (message && !window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
}

function initDialogCloseButtons() {
    document.querySelectorAll('[data-close-dialog]').forEach((button) => {
        button.addEventListener('click', () => {
            const dialogId = button.getAttribute('data-close-dialog');
            const dialog = dialogId ? document.getElementById(dialogId) : null;

            if (dialog instanceof HTMLDialogElement) {
                dialog.close();
            }
        });
    });
}

function initAccountMenus() {
    const menus = document.querySelectorAll('[data-account-menu]');

    if (!menus.length) {
        return;
    }

    const closeMenu = (menu) => {
        menu.classList.remove('is-open');
        menu.querySelector('[data-account-toggle]')?.setAttribute('aria-expanded', 'false');
    };

    menus.forEach((menu) => {
        const toggle = menu.querySelector('[data-account-toggle]');

        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();

            const isOpen = menu.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(isOpen));
        });
    });

    document.addEventListener('click', (event) => {
        menus.forEach((menu) => {
            if (!menu.contains(event.target)) {
                closeMenu(menu);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        menus.forEach(closeMenu);
    });
}

function initMobileNavigation() {
    document.querySelectorAll('[data-mobile-navigation-toggle]').forEach((toggle) => {
        const targetId = toggle.getAttribute('aria-controls');
        const target = targetId ? document.getElementById(targetId) : null;

        if (!target) {
            return;
        }

        const setOpen = (isOpen) => {
            target.classList.toggle('hidden', !isOpen);
            toggle.setAttribute('aria-expanded', String(isOpen));
        };

        toggle.addEventListener('click', () => {
            setOpen(target.classList.contains('hidden'));
        });
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

(() => {
    const toggle = document.getElementById('sidebar-toggle');
    if (!toggle) {
        return;
    }

    const collapsedKey = 'admin-sidebar-collapsed';
    const desktop = window.matchMedia('(min-width: 901px)');

    if (desktop.matches && sessionStorage.getItem(collapsedKey) === 'true') {
        toggle.checked = true;
    }
    sessionStorage.removeItem(collapsedKey);

    document.addEventListener('click', event => {
        if (event.defaultPrevented || !desktop.matches || !(event.target instanceof Element)) {
            return;
        }

        const link = event.target.closest('.dashboard a[href]');
        if (!link || link.target === '_blank') {
            return;
        }

        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin
            || !destination.pathname.includes('/admin/')
            || destination.pathname.endsWith('/logout.php')) {
            return;
        }

        toggle.checked = true;
        sessionStorage.setItem(collapsedKey, 'true');
    });
})();

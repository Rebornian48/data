/* global SorterCore */
(function () {
    'use strict';

    const items = window.SORTER_ITEMS || [];

    const originCheckboxes = () => Array.from(document.querySelectorAll('.filter-origin'));
    const releasedCheckboxes = () => Array.from(document.querySelectorAll('.filter-released'));
    const searchInput = () => document.getElementById('filter-search');

    const filter = {
        compute() {
            const origins = new Set(originCheckboxes().filter(c => c.checked).map(c => c.value));
            const released = new Set(
                releasedCheckboxes().filter(c => c.checked).map(c => c.value) // 'released' | 'unreleased'
            );
            const q = (searchInput().value || '').trim().toLowerCase();

            return items.filter(s => {
                const state = s.released ? 'released' : 'unreleased';
                if (!released.has(state)) return false;
                if (!origins.has(s.origin_group || '')) return false;
                if (q && !s.name.toLowerCase().includes(q)) return false;
                return true;
            });
        },
        watch(onChange) {
            originCheckboxes().forEach(c => c.addEventListener('change', onChange));
            releasedCheckboxes().forEach(c => c.addEventListener('change', onChange));
            searchInput().addEventListener('input', onChange);
            document.getElementById('origin-all').addEventListener('click', () => {
                originCheckboxes().forEach(c => c.checked = true);
                onChange();
            });
            document.getElementById('origin-none').addEventListener('click', () => {
                originCheckboxes().forEach(c => c.checked = false);
                onChange();
            });
        },
    };

    SorterCore.start({
        items,
        filter,
        formatSubtitle(item) {
            const parts = [];
            if (item.origin_group) parts.push(item.origin_group);
            if (item.single) parts.push(item.single.title);
            return parts.join(' • ');
        },
        formatBadge(item) {
            return {
                text: item.released ? 'RILIS' : 'BELUM',
                cssClass: item.released ? 'neo-badge-aktif' : 'neo-badge-lulus',
            };
        },
    });
})();

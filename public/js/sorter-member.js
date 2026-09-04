/* global SorterCore */
(function () {
    'use strict';

    const items = window.SORTER_ITEMS || [];

    const genCheckboxes = () => Array.from(document.querySelectorAll('.filter-gen'));
    const statusCheckboxes = () => Array.from(document.querySelectorAll('.filter-status'));

    const filter = {
        compute() {
            const gens = new Set(genCheckboxes().filter(c => c.checked).map(c => parseInt(c.value, 10)));
            const statuses = new Set(statusCheckboxes().filter(c => c.checked).map(c => c.value));
            return items.filter(m => {
                if (!statuses.has(m.status)) return false;
                if (!m.generation) return false;
                return gens.has(m.generation.id);
            });
        },
        watch(onChange) {
            genCheckboxes().forEach(c => c.addEventListener('change', onChange));
            statusCheckboxes().forEach(c => c.addEventListener('change', onChange));
            document.getElementById('gen-all').addEventListener('click', () => {
                genCheckboxes().forEach(c => c.checked = true);
                onChange();
            });
            document.getElementById('gen-none').addEventListener('click', () => {
                genCheckboxes().forEach(c => c.checked = false);
                onChange();
            });
        },
    };

    SorterCore.start({
        items,
        filter,
        formatSubtitle(item) {
            const code = item.generation ? item.generation.code : '';
            const full = (item.full_name && item.full_name !== item.name) ? item.full_name : '';
            return [code, full].filter(Boolean).join(' • ');
        },
        formatBadge(item) {
            if (!item.status) return null;
            return {
                text: String(item.status).toUpperCase(),
                cssClass: item.status === 'Aktif' ? 'neo-badge-aktif' : 'neo-badge-lulus',
            };
        },
    });
})();

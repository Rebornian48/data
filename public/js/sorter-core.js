/* global html2canvas */
/* eslint-disable security/detect-object-injection, no-unsanitized/property */
(function () {
    'use strict';

    window.SorterCore = window.SorterCore || {};

    // Public entry point. Every sorter type page wires its filter panel then calls this.
    //
    // config = {
    //   items: Item[],                           // all pool candidates (unfiltered)
    //   filter: {
    //     compute: () => Item[],                 // returns currently-filtered subset
    //     watch:   (onChange: () => void) => void // register a change handler
    //   },
    //   formatSubtitle?: (item) => string,       // small line under the name
    //   formatBadge?:    (item) => ({text,cssClass}|null),  // right-side result badge
    // }
    window.SorterCore.start = function (config) {
        const filter = config.filter;

        const formatSubtitle = config.formatSubtitle || function (item) {
            const gen = item.generation ? item.generation.name : '';
            const full = (item.full_name && item.full_name !== item.name) ? item.full_name : '';
            return [gen, full].filter(Boolean).join(' • ');
        };
        const formatBadge = config.formatBadge || function (item) {
            if (!item.status) return null;
            return {
                text: String(item.status).toUpperCase(),
                cssClass: item.status === 'Aktif' ? 'neo-badge-aktif' : 'neo-badge-lulus',
            };
        };

        // ---------- DOM refs ----------
        const $ = (id) => document.getElementById(id);

        const stageFilter = $('stage-filter');
        const stageSort = $('stage-sort');
        const stageResult = $('stage-result');

        const filterCount = $('filter-count');
        const filterHint = $('filter-hint');
        const btnStart = $('btn-start');

        const cardLeft = $('card-left');
        const cardRight = $('card-right');
        const cardTie = $('card-tie');
        const btnUndo = $('btn-undo');
        const btnRestart = $('btn-restart');

        const imgLeft = $('img-left');
        const phLeft = $('ph-left');
        const nameLeft = $('name-left');
        const subLeft = $('sub-left');

        const imgRight = $('img-right');
        const phRight = $('ph-right');
        const nameRight = $('name-right');
        const subRight = $('sub-right');

        const progressBar = $('progress-bar');
        const progressPct = $('progress-pct');
        const battleNum = $('battle-num');

        const resultList = $('result-list');
        const btnAgain = $('btn-again');
        const btnCopy = $('btn-copy');
        const btnShot = $('btn-shot');

        // ---------- Filter counting (delegated to filter module) ----------
        function updateFilterCount() {
            const n = filter.compute().length;
            filterCount.textContent = n;
            const ok = n >= 2;
            btnStart.disabled = !ok;
            filterHint.textContent = ok ? `(siap di-sort)` : `(minimal 2)`;
        }
        filter.watch(updateFilterCount);
        updateFilterCount();

        // ---------- Sorter state ----------
        let state = null;
        let backup = null;

        function newState(pool) {
            const n = pool.length;
            const sortData = [];
            const parentData = [];

            sortData.push(pool.map((_, i) => i));
            parentData.push(-1);

            for (let i = 0; i < sortData.length; i++) {
                const arr = sortData[i];
                if (arr.length <= 1) continue;
                const mid = Math.ceil(arr.length / 2);
                sortData.push(arr.slice(0, mid));
                parentData.push(i);
                sortData.push(arr.slice(mid));
                parentData.push(i);
            }

            let total = 0;
            for (let i = 1; i < sortData.length; i++) total += sortData[i].length;

            return {
                pool,
                sortData,
                parentData,
                equalData: new Array(n).fill(-1),
                recordData: [],
                leftList: sortData.length - 2,
                rightList: sortData.length - 1,
                leftIdx: 0,
                rightIdx: 0,
                completed: 0,
                total,
                battles: 0,
                finished: sortData.length === 1,
            };
        }

        function deepCloneState(s) {
            return {
                pool: s.pool,
                sortData: s.sortData.map(a => a.slice()),
                parentData: s.parentData.slice(),
                equalData: s.equalData.slice(),
                recordData: s.recordData.slice(),
                leftList: s.leftList,
                rightList: s.rightList,
                leftIdx: s.leftIdx,
                rightIdx: s.rightIdx,
                completed: s.completed,
                total: s.total,
                battles: s.battles,
                finished: s.finished,
            };
        }

        function countUp(side) {
            if (side === 0) {
                state.recordData.push(state.sortData[state.leftList][state.leftIdx]);
                state.leftIdx++;
            } else {
                state.recordData.push(state.sortData[state.rightList][state.rightIdx]);
                state.rightIdx++;
            }
            state.completed++;
        }

        function finishCurrentMerge() {
            const parent = state.parentData[state.leftList];
            state.sortData[parent] = state.recordData.slice();

            state.sortData.pop(); state.sortData.pop();
            state.parentData.pop(); state.parentData.pop();

            state.recordData = [];
            state.leftIdx = 0;
            state.rightIdx = 0;
            state.leftList -= 2;
            state.rightList -= 2;
        }

        function sortStep(selectId) {
            if (!state || state.finished) return;

            backup = deepCloneState(state);

            if (selectId !== 1) {
                countUp(0);
                while (
                    state.leftIdx < state.sortData[state.leftList].length &&
                    state.equalData[state.recordData[state.recordData.length - 1]] === state.sortData[state.leftList][state.leftIdx]
                ) {
                    countUp(0);
                }
            }

            if (selectId === 0) {
                const lastLeft = state.recordData[state.recordData.length - 1];
                const currentRight = state.sortData[state.rightList][state.rightIdx];
                state.equalData[lastLeft] = currentRight;
            }

            if (selectId !== -1) {
                countUp(1);
                while (
                    state.rightIdx < state.sortData[state.rightList].length &&
                    state.equalData[state.recordData[state.recordData.length - 1]] === state.sortData[state.rightList][state.rightIdx]
                ) {
                    countUp(1);
                }
            }

            if (state.leftIdx >= state.sortData[state.leftList].length) {
                while (state.rightIdx < state.sortData[state.rightList].length) countUp(1);
            } else if (state.rightIdx >= state.sortData[state.rightList].length) {
                while (state.leftIdx < state.sortData[state.leftList].length) countUp(0);
            }

            if (
                state.leftIdx >= state.sortData[state.leftList].length &&
                state.rightIdx >= state.sortData[state.rightList].length
            ) {
                finishCurrentMerge();
                if (state.leftList < 0) state.finished = true;
            }

            state.battles++;
            render();
        }

        function undo() {
            if (!backup) return;
            state = backup;
            backup = null;
            render();
        }

        // ---------- Rendering ----------
        function initialsBg(item) {
            return (item.name || '?').trim().charAt(0).toUpperCase();
        }

        function setSide(side, itemIdx) {
            const item = state.pool[itemIdx];
            const img = side === 'left' ? imgLeft : imgRight;
            const ph = side === 'left' ? phLeft : phRight;
            const name = side === 'left' ? nameLeft : nameRight;
            const sub = side === 'left' ? subLeft : subRight;

            if (item.photo) {
                img.src = item.photo;
                img.alt = item.name;
                img.classList.remove('hidden');
                ph.classList.add('hidden');
            } else {
                img.classList.add('hidden');
                ph.classList.remove('hidden');
                ph.textContent = initialsBg(item);
            }
            name.textContent = item.name;
            sub.textContent = formatSubtitle(item);
        }

        function render() {
            if (state.finished) {
                stageSort.classList.add('hidden');
                stageResult.classList.remove('hidden');
                renderResult();
                return;
            }
            stageResult.classList.add('hidden');
            stageSort.classList.remove('hidden');

            const leftIdx = state.sortData[state.leftList][state.leftIdx];
            const rightIdx = state.sortData[state.rightList][state.rightIdx];
            setSide('left', leftIdx);
            setSide('right', rightIdx);

            const pct = state.total === 0 ? 100 : Math.floor((state.completed / state.total) * 100);
            progressBar.style.width = pct + '%';
            progressPct.textContent = pct;
            battleNum.textContent = state.battles + 1;

            btnUndo.disabled = !backup;
        }

        // Ranking modes:
        //   'unique' : 1,2,3,4 always unique
        //   'seq'    : ties share rank, next = +1 → 1,1,2,3
        //   'skip'   : ties share rank, next skips → 1,1,3,4
        let rankMode = 'unique';

        function computeRanks(finalOrder) {
            const n = finalOrder.length;
            const ranks = new Array(n);
            if (rankMode === 'unique') {
                for (let i = 0; i < n; i++) ranks[i] = i + 1;
                return ranks;
            }
            let cur = 1;
            let same = 1;
            for (let i = 0; i < n; i++) {
                ranks[i] = cur;
                if (i < n - 1) {
                    const tie = state.equalData[finalOrder[i]] === finalOrder[i + 1];
                    if (rankMode === 'seq') {
                        if (!tie) cur++;
                    } else {
                        if (tie) same++;
                        else { cur += same; same = 1; }
                    }
                }
            }
            return ranks;
        }

        function renderResult() {
            const finalOrder = state.sortData[0];
            const ranks = computeRanks(finalOrder);
            const html = finalOrder.map((idx, i) => {
                const item = state.pool[idx];
                const rank = ranks[i];
                const isTiePrev = i > 0 && ranks[i - 1] === rank;
                const photoBlock = item.photo
                    ? `<img src="${escapeAttr(item.photo)}" alt="" class="w-full h-full object-cover">`
                    : `<div class="w-full h-full flex items-center justify-center text-lg display text-black">${escapeHtml(initialsBg(item))}</div>`;
                const rowBg = isTiePrev ? 'background:#ff6b9d;' : (i % 2 === 0 ? 'background:#fff;' : 'background:#fef2d0;');
                const badge = formatBadge(item);
                const badgeHtml = badge
                    ? `<div class="text-xs ${escapeAttr(badge.cssClass)}">${escapeHtml(badge.text)}</div>`
                    : '';
                return `
                    <div class="flex items-center gap-4 p-3 neo-card" style="${rowBg}">
                        <div class="w-12 text-center display text-2xl text-black">${rank}</div>
                        <div class="w-14 h-14 rounded-full overflow-hidden neo-avatar shrink-0">${photoBlock}</div>
                        <div class="min-w-0 flex-1">
                            <div class="display text-black truncate">${escapeHtml(item.name)}</div>
                            <div class="text-xs font-bold truncate">${escapeHtml(formatSubtitle(item))}</div>
                        </div>
                        ${badgeHtml}
                    </div>
                `;
            }).join('');
            resultList.innerHTML = html;
        }

        function escapeHtml(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        }
        function escapeAttr(s) { return escapeHtml(s); }

        // ---------- Event wiring ----------
        btnStart.addEventListener('click', () => {
            const pool = filter.compute();
            if (pool.length < 2) return;
            state = newState(pool);
            backup = null;
            stageFilter.classList.add('hidden');
            if (state.finished) {
                stageResult.classList.remove('hidden');
                renderResult();
            } else {
                stageSort.classList.remove('hidden');
                render();
            }
        });

        cardLeft.addEventListener('click', () => sortStep(-1));
        cardRight.addEventListener('click', () => sortStep(1));
        cardTie.addEventListener('click', () => sortStep(0));
        btnUndo.addEventListener('click', undo);
        btnRestart.addEventListener('click', () => {
            if (!confirm('Restart? Progres saat ini akan hilang.')) return;
            state = null; backup = null;
            stageSort.classList.add('hidden');
            stageResult.classList.add('hidden');
            stageFilter.classList.remove('hidden');
        });
        btnAgain.addEventListener('click', () => {
            state = null; backup = null;
            stageResult.classList.add('hidden');
            stageFilter.classList.remove('hidden');
        });

        // Keyboard
        document.addEventListener('keydown', (e) => {
            if (stageSort.classList.contains('hidden')) return;
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
            if (e.key === 'ArrowLeft') { e.preventDefault(); sortStep(-1); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); sortStep(1); }
            else if (e.key === 'ArrowDown' || e.key === ' ') { e.preventDefault(); sortStep(0); }
            else if (e.key === 'u' || e.key === 'U') { e.preventDefault(); undo(); }
        });

        // Ranking toggles
        document.querySelectorAll('.rank-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                rankMode = btn.dataset.rank;
                document.querySelectorAll('.rank-toggle').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                renderResult();
            });
        });

        // Copy
        btnCopy.addEventListener('click', async () => {
            const finalOrder = state.sortData[0];
            const ranks = computeRanks(finalOrder);
            const text = finalOrder.map((idx, i) => `${ranks[i]}. ${state.pool[idx].name}`).join('\n');
            try {
                await navigator.clipboard.writeText(text);
                btnCopy.textContent = 'TERSALIN ✓';
                setTimeout(() => btnCopy.textContent = 'SALIN TEKS', 1500);
            } catch {
                const ta = document.createElement('textarea');
                ta.value = text; document.body.appendChild(ta); ta.select();
                document.execCommand('copy'); document.body.removeChild(ta);
                btnCopy.textContent = 'TERSALIN ✓';
                setTimeout(() => btnCopy.textContent = 'SALIN TEKS', 1500);
            }
        });

        // Screenshot
        btnShot.addEventListener('click', async () => {
            if (typeof html2canvas === 'undefined') { alert('Modul screenshot belum ter-load, coba lagi sebentar.'); return; }
            const canvas = await html2canvas(resultList, { backgroundColor: '#fef2d0', scale: 2 });
            const link = document.createElement('a');
            link.download = 'jkt48-sorter-result.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    };
})();

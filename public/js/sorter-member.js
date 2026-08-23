(function () {
    'use strict';

    const items = window.SORTER_ITEMS || [];

    // ---------- DOM refs ----------
    const $ = (id) => document.getElementById(id);

    const stageFilter = $('stage-filter');
    const stageSort = $('stage-sort');
    const stageResult = $('stage-result');

    const filterCount = $('filter-count');
    const filterHint = $('filter-hint');
    const btnStart = $('btn-start');

    const genCheckboxes = () => Array.from(document.querySelectorAll('.filter-gen'));
    const statusCheckboxes = () => Array.from(document.querySelectorAll('.filter-status'));

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

    // ---------- Filter counting ----------
    function currentFilterSet() {
        const gens = new Set(genCheckboxes().filter(c => c.checked).map(c => parseInt(c.value, 10)));
        const statuses = new Set(statusCheckboxes().filter(c => c.checked).map(c => c.value));
        return items.filter(m => {
            if (!statuses.has(m.status)) return false;
            if (!m.generation) return false;
            return gens.has(m.generation.id);
        });
    }

    function updateFilterCount() {
        const n = currentFilterSet().length;
        filterCount.textContent = n;
        const ok = n >= 2;
        btnStart.disabled = !ok;
        filterHint.textContent = ok ? `(siap di-sort)` : `(minimal 2)`;
    }

    genCheckboxes().forEach(c => c.addEventListener('change', updateFilterCount));
    statusCheckboxes().forEach(c => c.addEventListener('change', updateFilterCount));

    $('gen-all').addEventListener('click', () => {
        genCheckboxes().forEach(c => c.checked = true);
        updateFilterCount();
    });
    $('gen-none').addEventListener('click', () => {
        genCheckboxes().forEach(c => c.checked = false);
        updateFilterCount();
    });

    updateFilterCount();

    // ---------- Sorter state ----------
    let state = null;
    let backup = null;

    function newState(pool) {
        // pool = array of item objects (order = index used by sorter)
        const n = pool.length;
        const sortData = [];
        const parentData = [];

        sortData.push(pool.map((_, i) => i));
        parentData.push(-1);

        // Recursive split. Use index-based traversal.
        for (let i = 0; i < sortData.length; i++) {
            const arr = sortData[i];
            if (arr.length <= 1) continue;
            const mid = Math.ceil(arr.length / 2);
            const left = arr.slice(0, mid);
            const right = arr.slice(mid);
            sortData.push(left);
            parentData.push(i);
            sortData.push(right);
            parentData.push(i);
        }

        // Total operations for progress bar = sum of all sublist sizes minus root.
        // (Every element except in root gets moved into parent once.)
        let total = 0;
        for (let i = 1; i < sortData.length; i++) total += sortData[i].length;

        // Pointer to the last pair (leaves at end).
        // Sublists are pushed in left/right pairs after split, so last two entries are a pair.
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
            finished: sortData.length === 1, // n <= 1
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

    // Move one element (side 0=left, 1=right) into recordData, advance pointer.
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

    // Advance to the next merge pair once current pair exhausted.
    function finishCurrentMerge() {
        // Write recordData into parent slot.
        const parent = state.parentData[state.leftList];
        state.sortData[parent] = state.recordData.slice();

        // Pop the two sublists (both children of the same parent).
        state.sortData.pop(); state.sortData.pop();
        state.parentData.pop(); state.parentData.pop();

        state.recordData = [];
        state.leftIdx = 0;
        state.rightIdx = 0;
        state.leftList -= 2;
        state.rightList -= 2;

        // Skip past sublists of size 1 (already sorted trivially — no merge needed at this level).
        // In the tree we built, all non-leaf nodes have two children of length >=1, but a size-1 node has no children.
        // Since we only push children when arr.length > 1, every remaining sublist at leftList/rightList is a valid pair.
    }

    // Handle user choice. selectId: -1 left, 0 tie, 1 right.
    function sortStep(selectId) {
        if (!state || state.finished) return;

        backup = deepCloneState(state);

        // Advance current pair according to choice.
        if (selectId !== 1) {
            // pick left (or tie): consume left element
            countUp(0);
            // consume any tie-chained elements after it that come from same sublist
            while (
                state.leftIdx < state.sortData[state.leftList].length &&
                state.equalData[state.recordData[state.recordData.length - 1]] === state.sortData[state.leftList][state.leftIdx]
            ) {
                countUp(0);
            }
        }

        if (selectId === 0) {
            // record tie link: last-left-element -> current-right-element
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

        // Drain if one side exhausted.
        if (state.leftIdx >= state.sortData[state.leftList].length) {
            while (state.rightIdx < state.sortData[state.rightList].length) countUp(1);
        } else if (state.rightIdx >= state.sortData[state.rightList].length) {
            while (state.leftIdx < state.sortData[state.leftList].length) countUp(0);
        }

        // Both drained → merge done.
        if (
            state.leftIdx >= state.sortData[state.leftList].length &&
            state.rightIdx >= state.sortData[state.rightList].length
        ) {
            finishCurrentMerge();
            if (state.leftList < 0) {
                state.finished = true;
            }
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
        sub.textContent = (item.generation ? item.generation.code : '') +
                          (item.full_name && item.full_name !== item.name ? ' • ' + item.full_name : '');
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
    //   'unique' : 1,2,3,4 always unique (even after tie)
    //   'seq'    : ties share rank, next = +1 → 1,1,2,3
    //   'skip'   : ties share rank, next skips → 1,1,3,4
    let rankMode = 'unique';

    function computeRanks(finalOrder) {
        // finalOrder = array of pool indices
        const n = finalOrder.length;
        const ranks = new Array(n);
        if (rankMode === 'unique') {
            for (let i = 0; i < n; i++) ranks[i] = i + 1;
            return ranks;
        }
        // tie-aware
        let cur = 1;
        let same = 1;
        for (let i = 0; i < n; i++) {
            ranks[i] = cur;
            if (i < n - 1) {
                const tie = state.equalData[finalOrder[i]] === finalOrder[i + 1];
                if (rankMode === 'seq') {
                    if (tie) {
                        // next stays same
                    } else {
                        cur++;
                    }
                } else { // skip
                    if (tie) {
                        same++;
                    } else {
                        cur += same;
                        same = 1;
                    }
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
                : `<div class="w-full h-full flex items-center justify-center text-white text-lg font-bold gradient-brand">${escapeHtml(initialsBg(item))}</div>`;
            return `
                <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-700/40 rounded-lg p-3 border border-slate-200 dark:border-slate-700">
                    <div class="w-10 text-center font-bold text-slate-900 dark:text-slate-100 ${isTiePrev ? 'text-brand' : ''}">${rank}</div>
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 shrink-0">${photoBlock}</div>
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-slate-900 dark:text-slate-100 truncate">${escapeHtml(item.name)}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            ${item.generation ? escapeHtml(item.generation.name) : ''}
                            ${item.full_name && item.full_name !== item.name ? ' • ' + escapeHtml(item.full_name) : ''}
                        </div>
                    </div>
                    <div class="text-xs px-2 py-0.5 rounded ${item.status === 'Aktif' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300'}">${escapeHtml(item.status)}</div>
                </div>
            `;
        }).join('');
        resultList.innerHTML = html;
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function escapeAttr(s) { return escapeHtml(s); }

    // ---------- Event wiring ----------
    btnStart.addEventListener('click', () => {
        const pool = currentFilterSet();
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
            document.querySelectorAll('.rank-toggle').forEach(b => {
                b.classList.remove('bg-brand', 'text-white');
                b.classList.add('text-slate-600', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700');
            });
            btn.classList.add('bg-brand', 'text-white');
            btn.classList.remove('text-slate-600', 'dark:text-slate-300', 'hover:bg-slate-100', 'dark:hover:bg-slate-700');
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
            btnCopy.textContent = 'Tersalin ✓';
            setTimeout(() => btnCopy.textContent = 'Salin Teks', 1500);
        } catch {
            const ta = document.createElement('textarea');
            ta.value = text; document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); document.body.removeChild(ta);
            btnCopy.textContent = 'Tersalin ✓';
            setTimeout(() => btnCopy.textContent = 'Salin Teks', 1500);
        }
    });

    // Screenshot
    btnShot.addEventListener('click', async () => {
        if (typeof html2canvas === 'undefined') { alert('Modul screenshot belum ter-load, coba lagi sebentar.'); return; }
        const target = resultList;
        const canvas = await html2canvas(target, { backgroundColor: null, scale: 2 });
        const link = document.createElement('a');
        link.download = 'jkt48-sorter-result.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
})();

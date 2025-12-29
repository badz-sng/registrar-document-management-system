<!DOCTYPE html>
<html lang="en" id="main-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst(auth()->user()->role ?? 'Dashboard') }}</title>
    {{-- Load compiled assets (Alpine + Tailwind via Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="sticky top-0 z-50 bg-white shadow p-4 flex justify-between items-center">
    <!-- Dark mode toggle removed -->
        <a href="/">
    <div class="font-bold text-xl">
            {{ ucfirst(auth()->user()->role) }} Dashboard
        </div>
        </a>
        <div class="relative inline-block text-left">
            <!-- User Menu -->
            <button type="button" class="flex items-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300" id="user-menu-button" onclick="document.getElementById('user-menu').classList.toggle('hidden')">
                {{ Auth::user()->name }}
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="user-menu" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg hidden z-10">
                <a href="/" class="block px-4 py-2 hover:bg-gray-100">{{ ucfirst(auth()->user()->role) }} Homepage</a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Account</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-red-500">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>
    @stack('scripts')
<!-- Table pagination: automatically adds per-table pagination controls for large tables.
     Usage: All <table> elements will get client-side pagination (rows-per-page: 10/20/50).
     To disable pagination for a specific table, add the attribute: data-no-paginate="true" on the <table> element.

    To set a different default rows-per-page, add the attribute: data-rows-default="20" (or 10, 50) on the <table> element.

    The pagination script is placed only in this layout to avoid redundancy across multiple views since this layout is used by all roles.
-->

<script>
/**
 * Client-side table pagination
 *
 * What it does:
 *  - Automatically attaches a small pagination UI to each <table> on the page (unless opted-out).
 *  - Provides a rows-per-page selector (10/20/50), Prev/Next navigation, page indicator, and a compact info text.
 *
 * Why it's here:
 *  - Provides quick client-side pagination for long tables without changing backend routes or controller logic.
 *
 * Usage:
 *  - The script runs on DOMContentLoaded and locates all <table> elements.
 *  - Opt-out: add data-no-paginate="true" to a <table> to skip pagination.
 *  - Per-table default: add data-rows-default="20" to set default rows-per-page for that table.
 *
 * Notes:
 *  - Rows are shown/hidden by setting inline style.display on each <tr>.
 *  - If a table contains multiple TBODY sections, only the first TBODY (tBodies[0]) is used for pagination.
 */
document.addEventListener('DOMContentLoaded', function () {
    /**
     * buildControls(table)
     *
     * Creates and returns a set of DOM elements used as pagination controls for the given table.
     *
     * Inputs:
     *  - table: HTMLTableElement the controls are for.
     *
     * Returns: an object { controls, perPageSelect, info, prevBtn, nextBtn, pageDisplay }
     *  - controls: wrapper element containing the whole UI
     *  - perPageSelect: <select> element for rows-per-page
     *  - info: <div> for compact status text
     *  - prevBtn, nextBtn: navigation buttons
     *  - pageDisplay: current page / total pages text element
     */
    function buildControls(table) {
        const controls = document.createElement('div');
        controls.className = 'flex items-center justify-between my-3 gap-4';

        const perPageSelect = document.createElement('select');
        perPageSelect.className = 'border rounded px-2 py-1 text-sm';
        [5 ,10, 20, 50].forEach(function (n) {
            const opt = document.createElement('option');
            opt.value = n; opt.textContent = n + ' rows';
            perPageSelect.appendChild(opt);
        });
        perPageSelect.value = table.dataset.rowsDefault || 5;

        const info = document.createElement('div');
        info.className = 'text-sm text-gray-700';

        const nav = document.createElement('div');
        nav.className = 'flex items-center gap-2';
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button'; prevBtn.className = 'px-2 py-1 border rounded text-sm bg-white hover:bg-gray-50'; prevBtn.textContent = 'Prev';
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button'; nextBtn.className = 'px-2 py-1 border rounded text-sm bg-white hover:bg-gray-50'; nextBtn.textContent = 'Next';

        const pageDisplay = document.createElement('span');
        pageDisplay.className = 'px-2 text-sm';

        nav.appendChild(prevBtn);
        nav.appendChild(pageDisplay);
        nav.appendChild(nextBtn);

        const left = document.createElement('div');
        left.className = 'flex items-center gap-2';
        const label = document.createElement('label');
        label.className = 'text-sm text-gray-700';
        label.textContent = 'Rows:';
        left.appendChild(label);
        left.appendChild(perPageSelect);

        controls.appendChild(left);
        controls.appendChild(info);
        controls.appendChild(nav);

        return { controls, perPageSelect, info, prevBtn, nextBtn, pageDisplay };
    }

    // Attach paginator to all tables unless explicitly opted out
    document.querySelectorAll('table').forEach(function (table) {
        if (table.dataset.noPaginate === 'true') return;

        const tbody = table.tBodies && table.tBodies.length ? table.tBodies[0] : table;
        const allRows = Array.from(tbody.querySelectorAll('tr'));
        if (!allRows.length) return;

        const ui = buildControls(table);
        table.parentNode.insertBefore(ui.controls, table);

        // State
        let perPage = parseInt(ui.perPageSelect.value, 10) || 10;
        let currentPage = 1;

        /**
         * render()
         *
         * Renders the table rows based on current pagination state and updates UI.
         * No inputs. Side-effects: updates DOM (rows' display, info text, button disabled state).
         */
        function render() {
            const total = allRows.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            allRows.forEach(function (row, idx) {
                row.style.display = (idx >= start && idx < end) ? '' : 'none';
            });

            ui.info.textContent = 'Showing ' + Math.min(total, start + 1) + ' to ' + Math.min(total, end) + ' of ' + total + ' Entries';
            ui.pageDisplay.textContent = 'Page ' + currentPage + ' / ' + totalPages;
            ui.prevBtn.disabled = currentPage <= 1;
            ui.nextBtn.disabled = currentPage >= totalPages;
            ui.prevBtn.classList.toggle('opacity-50', ui.prevBtn.disabled);
            ui.nextBtn.classList.toggle('opacity-50', ui.nextBtn.disabled);
        }

        ui.perPageSelect.addEventListener('change', function () {
            perPage = parseInt(ui.perPageSelect.value, 10) || 10;
            currentPage = 1;
            render();
        });

        ui.prevBtn.addEventListener('click', function () { if (currentPage > 1) { currentPage--; render(); } });
        ui.nextBtn.addEventListener('click', function () { const totalPages = Math.max(1, Math.ceil(allRows.length / perPage)); if (currentPage < totalPages) { currentPage++; render(); } });

        // initial render
        render();
    });
});
</script>
</body>
</html>

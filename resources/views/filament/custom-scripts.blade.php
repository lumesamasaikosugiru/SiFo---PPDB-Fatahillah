



<style>
/*
 * ── FILAMENT FILTER INLINE BUTTON ──────────────────────────────────────
 * Geser tombol Apply Filters ke kanan, sejajar dengan filter fields
 * Bekerja dengan ->filtersLayout(AboveContent) + ->deferFilters()
 */
.fi-ta-filters-form {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: flex-end !important;
    gap: 0.5rem !important;
}
.fi-ta-filters-form > div:not([class*="col"]):not(.fi-fo-component-ctn) {
    flex: 0 0 auto !important;
    margin-left: auto !important;
}
/* Tombol Apply - warna hijau, ukuran sesuai filter fields */
.fi-ta-filters-form [wire\:click*="applyTableFilters"],
.fi-ta-filters-form button[type="submit"] {
    white-space: nowrap;
    align-self: flex-end;
    margin-bottom: 0 !important;
}
/* Filter fields grid */
[data-filters-layout="above-content"] .fi-ta-filters-form {
    display: grid !important;
    grid-template-columns: repeat(5, 1fr) auto !important;
    align-items: end !important;
    gap: 0.5rem !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Sidebar toggle: 1 icon untuk semua ukuran layar ─────────────────────
    // Strategy:
    // - Mobile  : Sembunyikan tombol bawaan Filament, pakai tombol custom kita
    // - Desktop : Sembunyikan tombol bawaan Filament, pakai tombol custom kita
    // - Satu tombol bekerja di semua ukuran layar
    // - Default sidebar: SHOW di desktop, HIDDEN di mobile

    // CSS: selalu sembunyikan tombol bawaan Filament di semua ukuran
    var sidebarStyle = document.createElement('style');
    sidebarStyle.textContent = `
        /* Sembunyikan tombol bawaan Filament di SEMUA ukuran */
        .fi-topbar-open-sidebar-btn { display: none !important; }
        /* Custom button: hidden by default, inject via JS */
        .ppdb-sidebar-toggle-btn { display: none !important; }
        @media (min-width: 1px) {
            .ppdb-sidebar-toggle-btn { display: flex !important; }
        }
    `;
    document.head.appendChild(sidebarStyle);

    function triggerSidebarToggle() {
        if (window.Alpine) {
            var store = window.Alpine.store('sidebar');
            if (store) {
                var isMobile = window.innerWidth < 1024;
                if (isMobile) {
                    // Mobile: toggle isOpen (sidebar overlay)
                    if (typeof store.isOpen !== 'undefined') {
                        store.isOpen ? store.close() : store.open();
                    } else {
                        store.open ? store.open() : null;
                    }
                } else {
                    // Desktop: toggle isOpenDesktop
                    if (typeof store.isOpenDesktop !== 'undefined') {
                        store.isOpenDesktop ? store.close() : store.open();
                    } else {
                        store.open ? store.open() : null;
                    }
                }
                return;
            }
        }
        // Fallback
        var sidebar = document.querySelector('.fi-sidebar, .fi-main-sidebar, [x-data*="sidebar"]');
        if (sidebar) sidebar.classList.toggle('fi-sidebar-open');
    }

    function injectSidebarToggle() {
        if (document.getElementById('ppdb-toggle-btn')) return;
        var topbar = document.querySelector('.fi-topbar, nav.fi-topbar, [data-topbar]');
        if (!topbar) return;

        var btn = document.createElement('button');
        btn.id        = 'ppdb-toggle-btn';
        btn.type      = 'button';
        btn.title     = 'Show/Hide Sidebar';
        btn.className = 'ppdb-sidebar-toggle-btn';
        btn.setAttribute('aria-label', 'Toggle sidebar');
        btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.8" stroke="currentColor" width="20" height="20">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>`;
        btn.style.cssText = 'align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;border:none;background:transparent;cursor:pointer;color:inherit;margin-right:4px;flex-shrink:0;transition:background 0.15s;';
        btn.addEventListener('mouseover', function() { btn.style.background = 'rgba(0,0,0,0.06)'; });
        btn.addEventListener('mouseout',  function() { btn.style.background = 'transparent'; });
        btn.addEventListener('click', triggerSidebarToggle);

        var firstChild = topbar.firstElementChild;
        firstChild ? topbar.insertBefore(btn, firstChild) : topbar.appendChild(btn);
    }

    injectSidebarToggle();
    setTimeout(injectSidebarToggle, 400);
    setTimeout(injectSidebarToggle, 1200);
    document.addEventListener('livewire:navigated', function () {
        document.getElementById('ppdb-toggle-btn')?.remove();
        setTimeout(injectSidebarToggle, 200);
    });

    // ── 2. SweetAlert confirm pada logout ─────────────────────────────────────
    function attachLogoutConfirm() {
        document.querySelectorAll('button, [role="button"]').forEach(function (btn) {
            if (btn.dataset.swalLogout) return;
            var txt = (btn.textContent || '').trim().toLowerCase();
            var wc  = btn.getAttribute('wire:click') || '';
            if (txt === 'sign out' || txt === 'keluar' || wc.includes('logout')) {
                btn.dataset.swalLogout = '1';
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    Swal.fire({
                        title: 'Keluar dari sistem?',
                        text: 'Apakah Anda yakin ingin sign out?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor:  '#6b7280',
                        confirmButtonText:  'Ya, Keluar',
                        cancelButtonText:   'Batal',
                        reverseButtons: true,
                    }).then(function (result) {
                        if (!result.isConfirmed) return;
                        var form = document.querySelector('form[action*="logout"]');
                        if (form) { form.submit(); return; }
                        var csrf = document.querySelector('meta[name="csrf-token"]');
                        fetch('/admin/logout', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf ? csrf.content : '',
                                'Accept': 'application/json',
                            },
                        }).then(function () {
                            window.location.href = '/admin/login';
                        }).catch(function () {
                            window.location.href = '/admin/login';
                        });
                    });
                }, true);
            }
        });
    }

    attachLogoutConfirm();
    document.addEventListener('livewire:navigated', function () {
        setTimeout(attachLogoutConfirm, 300);
    });
    setTimeout(attachLogoutConfirm, 800);
    setTimeout(attachLogoutConfirm, 2000);
});
</script>

{{-- ═══════════════════════════════════════════════════════════════════════════
     AUTO-APPLY FILTER DARI URL QUERY STRING
     Dipakai ketika grafik drilldown redirect ke /admin/pendaftaran?tableFilters[...]
     JS ini membaca URL params dan inject ke Livewire table filter state
═══════════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';

    /**
     * Baca query string URL dan apply ke Filament Livewire table filter.
     * Format URL yang didukung (dari grafik drilldown Highcharts):
     *   /admin/pendaftaran?tableFilters[sekolah_id][value]=1&tableFilters[jurusan_id][value]=2
     *
     * Cara kerja:
     *  1. Parse URL params
     *  2. Cari Livewire component yang punya tableFilters (ListPendaftarans)
     *  3. Inject nilai filter via Livewire.$set
     *  4. Trigger applyTableFilters supaya data table langsung reload
     */
    function applyUrlFiltersToTable() {
        var url    = new URL(window.location.href);
        var params = url.searchParams;

        // Kumpulkan semua filter dari URL params
        var filters = {};
        params.forEach(function (value, key) {
            // key format: tableFilters[sekolah_id][value]
            var match = key.match(/^tableFilters\[([^\]]+)\]\[([^\]]+)\]$/);
            if (match) {
                var filterKey  = match[1]; // sekolah_id
                var filterProp = match[2]; // value / dari / sampai
                if (!filters[filterKey]) filters[filterKey] = {};
                filters[filterKey][filterProp] = value;
            }
        });

        if (Object.keys(filters).length === 0) return;

        // Hapus params dari URL (biar clean, tanpa reload)
        var cleanUrl = url.pathname;
        window.history.replaceState({}, '', cleanUrl);

        // Tunggu Livewire component siap
        function tryInject(attempt) {
            if (attempt > 30) return; // max 3 detik

            // Cari semua Livewire components
            var components = [];
            if (window.Livewire && window.Livewire.all) {
                components = window.Livewire.all();
            } else if (window.Livewire && window.Livewire.components) {
                components = Object.values(window.Livewire.components.componentsById || {});
            }

            // Cari component yang punya tableFilters (ListPendaftarans)
            var target = null;
            for (var i = 0; i < components.length; i++) {
                var c = components[i];
                var name = (c.name || c.__name || (c.$wire && c.$wire.__instance && c.$wire.__instance.name) || '');
                // Cari berdasarkan nama component atau keberadaan tableFilters
                if (
                    name.toLowerCase().includes('list-pendaftaran') ||
                    name.toLowerCase().includes('listpendaftaran') ||
                    (c.$wire && typeof c.$wire.get === 'function' && c.$wire.get('tableFilters') !== undefined)
                ) {
                    target = c.$wire || c;
                    break;
                }
            }

            // Fallback: ambil component pertama yang punya tableFilters
            if (!target) {
                for (var j = 0; j < components.length; j++) {
                    var cj = components[j];
                    if (cj.$wire) {
                        try {
                            var tf = cj.$wire.get('tableFilters');
                            if (tf !== undefined && tf !== null) {
                                target = cj.$wire;
                                break;
                            }
                        } catch (e) { /* skip */ }
                    }
                }
            }

            if (!target) {
                setTimeout(function () { tryInject(attempt + 1); }, 100);
                return;
            }

            // Inject semua filter satu per satu
            var filterKeys = Object.keys(filters);
            filterKeys.forEach(function (filterKey) {
                var filterData = filters[filterKey];
                Object.keys(filterData).forEach(function (prop) {
                    var livewirePath = 'tableFilters.' + filterKey + '.' + prop;
                    try {
                        target.set(livewirePath, filterData[prop]);
                    } catch (e) {
                        // Livewire v3 pakai $set
                        try { target.$set(livewirePath, filterData[prop]); } catch (e2) { /* skip */ }
                    }
                });
            });

            // Apply filter (trigger reload table)
            setTimeout(function () {
                try {
                    if (typeof target.applyTableFilters === 'function') {
                        target.applyTableFilters();
                    } else if (typeof target.call === 'function') {
                        target.call('applyTableFilters');
                    }
                } catch (e) { /* skip */ }
            }, 150);
        }

        // Mulai inject setelah Livewire dan DOM siap
        setTimeout(function () { tryInject(0); }, 500);
    }

    // Jalankan saat page load (termasuk Livewire SPA navigation)
    document.addEventListener('DOMContentLoaded', applyUrlFiltersToTable);
    document.addEventListener('livewire:navigated', applyUrlFiltersToTable);

    // Jika halaman sudah loaded (script di-inject setelah DOMContentLoaded)
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(applyUrlFiltersToTable, 200);
    }
})();
</script>

{{-- ═══════════════════════════════════════════════════════════════════════
     AUTO-APPLY FILTER DARI HIGHCHARTS DRILLDOWN (via sessionStorage)
     Dibaca saat halaman Pendaftaran load setelah klik jurusan di grafik
════════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    // Hanya jalankan di halaman Pendaftaran
    function isPendaftaranPage() {
        return window.location.pathname.indexOf('/admin/pendaftaran') !== -1
            && window.location.pathname.indexOf('/admin/pendaftaran/') === -1; // bukan detail
    }

    function applyChartFilter() {
        if (!isPendaftaranPage()) return;

        var sid, jid;
        try {
            sid = sessionStorage.getItem('ppdb_filter_sekolah');
            jid = sessionStorage.getItem('ppdb_filter_jurusan');
        } catch(e) { return; }

        if (!sid && !jid) return;

        // Hapus dari sessionStorage supaya tidak re-apply saat refresh manual
        try {
            sessionStorage.removeItem('ppdb_filter_sekolah');
            sessionStorage.removeItem('ppdb_filter_jurusan');
        } catch(e) {}

        // Tunggu Livewire component siap lalu inject filter
        function tryApply(attempt) {
            if (attempt > 40) return;

            var components = [];
            try {
                if (window.Livewire && window.Livewire.all) {
                    components = window.Livewire.all();
                }
            } catch(e) {}

            // Cari component yang punya tableFilters (ListPendaftarans)
            var target = null;
            for (var i = 0; i < components.length; i++) {
                var wire = components[i].$wire || components[i];
                try {
                    var tf = wire.get && wire.get('tableFilters');
                    if (tf !== undefined && tf !== null) {
                        target = wire;
                        break;
                    }
                } catch(e) {}
            }

            if (!target) {
                setTimeout(function () { tryApply(attempt + 1); }, 150);
                return;
            }

            // Set filter values
            try {
                if (sid) target.set('tableFilters.sekolah_id.value', sid);
                if (jid) target.set('tableFilters.jurusan_id.value', jid);
            } catch(e) {}

            // Apply filter
            setTimeout(function () {
                try {
                    if (typeof target.applyTableFilters === 'function') {
                        target.applyTableFilters();
                    } else if (typeof target.call === 'function') {
                        target.call('applyTableFilters');
                    }
                } catch(e) {}
            }, 200);
        }

        setTimeout(function () { tryApply(0); }, 600);
    }

    document.addEventListener('DOMContentLoaded', applyChartFilter);
    document.addEventListener('livewire:navigated', applyChartFilter);
    if (document.readyState !== 'loading') {
        setTimeout(applyChartFilter, 300);
    }
})();
</script>

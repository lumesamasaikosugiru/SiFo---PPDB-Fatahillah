<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chart</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body {
    width: 100%;
    font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
    background: #fff;
    overflow: hidden;
}
#hc-container { width: 100%; min-height: 420px; }
@keyframes sp { to { transform: rotate(360deg); } }
</style>
</head>
<body>

<div id="hc-container">
    <div id="hc-loading" style="display:flex;align-items:center;justify-content:center;height:420px;gap:10px;font-size:13px;color:#6b7280">
        <div style="width:20px;height:20px;border:2px solid #e5e7eb;border-top-color:#16a34a;border-radius:50%;animation:sp .7s linear infinite"></div>
        Memuat grafik Highcharts...
    </div>
</div>

<script>
var API_URL = "{{ route('chart.data') }}";
var CSRF    = '{{ csrf_token() }}';
</script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>

<script>
(function () {
    if (typeof Highcharts === 'undefined') {
        document.getElementById('hc-loading').innerHTML =
            '<span style="color:#ef4444">⚠️ Highcharts gagal dimuat. Periksa koneksi internet.</span>';
        return;
    }

    fetch(API_URL, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(function (r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function (data) {
        var el = document.getElementById('hc-loading');
        if (el) el.remove();

        var isDrill = data.mode === 'drilldown';

        Highcharts.chart('hc-container', {
            chart: {
                type:            'column',
                backgroundColor: '#fff',
                style:           { fontFamily: 'ui-sans-serif, system-ui, sans-serif' },
                animation:       { duration: 500 },
                // Tinggi chart: 80px per sekolah/jurusan + ruang legend + margin
                height: 480,
                events: {
                    render: function () {
                        try {
                            var h = document.body.scrollHeight + 20;
                            window.parent.document
                                .getElementById('ppdb-hc-frame')
                                .style.height = h + 'px';
                        } catch (e) {}
                    },
                },
            },

            title:         { text: null },
            credits:       { enabled: false },
            accessibility: { enabled: false },

            legend: {
                enabled:       true,
                layout:        'horizontal',
                align:         'center',
                verticalAlign: 'bottom',
                itemStyle:     { fontSize: '12px', fontWeight: '600', color: '#374151' },
                itemHoverStyle:{ color: '#16a34a' },
                // Symbol kotak kecil per warna sekolah
                symbolRadius:  3,
                symbolHeight:  12,
                symbolWidth:   12,
            },

            xAxis: {
                type:   'category',
                labels: {
                    style:    { fontSize: '12px', fontWeight: 'bold', color: '#374151' },
                    rotation: -20,
                    align:    'right',
                },
                title: {
                    text:  isDrill ? 'Sekolah / Jurusan' : 'Jurusan',
                    style: { fontSize: '13px', fontWeight: 'bold', color: '#374151' },
                },
                lineColor: '#e5e7eb',
                tickColor: '#e5e7eb',
            },

            yAxis: {
                min:           0,
                allowDecimals: false,
                title: {
                    text:  'Jumlah Pendaftar',
                    style: { fontSize: '13px', fontWeight: 'bold', color: '#374151' },
                },
                labels: { style: { fontSize: '12px', fontWeight: 'bold', color: '#374151' } },
                gridLineColor: '#f3f4f6',
            },

            tooltip: {
                backgroundColor: '#fff',
                borderColor:     '#e5e7eb',
                borderRadius:    8,
                shadow:          true,
                style:           { fontSize: '13px', color: '#111827' },
                useHTML:         true,
                formatter: function () {
                    var opts = this.point.options;
                    var tip  = '<b>' + this.point.name + '</b>'
                             + '<br/>Pendaftar: <b style="color:' + this.point.color + '">'
                             + this.y + '</b>';
                    if (this.point.drilldown) {
                        tip += '<br/><span style="color:#9ca3af;font-size:11px">🔍 Klik untuk lihat per jurusan</span>';
                    } else if (opts && opts.direct_url) {
                        tip += '<br/><span style="color:#16a34a;font-size:11px;font-weight:700">🔗 Klik untuk lihat daftar pendaftar</span>';
                    }
                    return tip;
                },
            },

            plotOptions: {
                column: {
                    borderRadius: 4,
                    pointPadding: 0.05,
                    groupPadding: 0.1,
                    dataLabels: {
                        enabled: true,
                        format:  '{point.y}',
                        style: {
                            fontSize:    '13px',
                            fontWeight:  'bold',
                            color:       '#374151',
                            textOutline: 'none',
                        },
                    },
                },
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                // Bar sekolah → Highcharts handle drilldown sendiri
                                if (this.drilldown) return;

                                // Bar jurusan → navigate via window.top (parent dari iframe)
                                var url = this.options.direct_url;
                                if (url) {
                                    try {
                                        // Simpan filter ke sessionStorage parent
                                        var sid = this.options.sekolah_id;
                                        var jid = this.options.jurusan_id;
                                        if (sid) window.top.sessionStorage.setItem('ppdb_filter_sekolah', String(sid));
                                        if (jid) window.top.sessionStorage.setItem('ppdb_filter_jurusan', String(jid));
                                    } catch (ex) {}

                                    // Navigate parent window langsung
                                    window.top.location.href = url;
                                }
                            },
                        },
                    },
                },
            },

            // Multi-series: 1 series per sekolah → legend bisa toggle per sekolah
            series: (isDrill ? data.series : [{
                name:         'Jumlah Pendaftar',
                colorByPoint: true,
                data:         data.series,
            }]),

            drilldown: isDrill ? {
                breadcrumbs: {
                    position: { align: 'right' },
                    style:    { color: '#374151', fontWeight: 'bold', fontSize: '12px' },
                },
                activeDataLabelStyle: {
                    color:          '#374151',
                    textDecoration: 'none',
                    textOutline:    'none',
                    fontWeight:     'bold',
                },
                series: data.drilldowns,
            } : undefined,
        });
    })
    .catch(function (e) {
        document.getElementById('hc-loading').innerHTML =
            '<span style="color:#ef4444;padding:20px;display:block;text-align:center">⚠️ Error: ' + e.message + '</span>';
        console.error('[PPDB Chart]', e);
    });
}());
</script>
</body>
</html>

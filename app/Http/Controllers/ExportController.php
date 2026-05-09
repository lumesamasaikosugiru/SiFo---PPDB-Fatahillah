<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftaran;
use App\Models\Pembayaran;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // ============================================================
    //  PENDAFTARAN
    // ============================================================

    public function pendaftaranPdf(Request $request)
    {
        $query = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'tahunAkademik', 'userVerifikator'])
            ->sekolah()
            ->orderBy('tanggal_submit', 'desc');

        $this->applyPendaftaranFilters($query, $request);

        $records     = $query->get();
        $dateFrom    = $request->date_from;
        $dateTo      = $request->date_to;
        $status      = $request->status;
        $sekolahNama = $request->sekolah_id ? Sekolah::find($request->sekolah_id)?->nama_sekolah : null;
        $jurusanNama = $request->jurusan_id ? Jurusan::find($request->jurusan_id)?->nama_jurusan : null;

        $html = view('exports.pendaftaran-pdf', compact(
            'records', 'dateFrom', 'dateTo', 'status', 'sekolahNama', 'jurusanNama'
        ))->render();

        $dompdf = new \Dompdf\Dompdf([
            'enable_html5_parser'    => true,
            'enable_remote'          => false,
            'font_dir'               => storage_path('fonts/'),
            'font_cache'             => storage_path('fonts/'),
            'temp_dir'               => sys_get_temp_dir(),
            'chroot'                 => base_path(),
            'default_font'           => 'DejaVu Sans',
            'enable_font_subsetting' => true,
            'dpi'                    => 96,
        ]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'pendaftaran_' . now()->format('Ymd_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function pendaftaranExcel(Request $request)
    {
        $query = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'tahunAkademik', 'userVerifikator'])
            ->sekolah()
            ->orderBy('tanggal_submit', 'desc');

        $this->applyPendaftaranFilters($query, $request);
        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pendaftaran');

        // Header info
        $sheet->setCellValue('A1', 'LAPORAN DATA PENDAFTARAN — PPDB Terpusat YPFC');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $info = 'Dicetak: ' . now()->format('d/m/Y H:i');
        if ($request->date_from || $request->date_to) {
            $info .= '   |   Periode: ' . ($request->date_from ?? '—') . ' s/d ' . ($request->date_to ?? '—');
        }
        if ($request->status)    $info .= '   |   Status: ' . ucfirst(str_replace('_', ' ', $request->status));
        if ($request->sekolah_id) $info .= '   |   Sekolah: ' . (Sekolah::find($request->sekolah_id)?->nama_sekolah ?? '-');

        $sheet->setCellValue('A2', $info);
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A2')->getFont()->getColor()->setARGB('FF6B7280');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Column headers (row 4)
        $headers = [
            'No', 'Kode Registrasi', 'Tahun Akademik', 'Sekolah Tujuan', 'Jurusan',
            'NISN', 'Nama Calon Murid', 'Jenis Kelamin', 'Asal Sekolah', 'Tahun Lulus',
            'Jalur Pendaftaran', 'Status', 'Tanggal Submit', 'Diverifikasi Oleh', 'Dibuat Oleh',
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }

        // Header style
        $headerRange = 'A4:O4';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Data rows
        $statusLabels = [
            'diproses'            => 'Diproses',
            'diverifikasi'        => 'Diverifikasi',
            'diterima'            => 'Diterima',
            'ditolak'             => 'Ditolak',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'pembayaran_diproses' => 'Pembayaran Diproses',
            'pembayaran_lunas'    => 'Pembayaran Lunas',
            'selesai'             => 'Selesai',
        ];

        $row = 5;
        foreach ($records as $i => $r) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $r->kode_regis);
            $sheet->setCellValue('C' . $row, $r->tahunAkademik?->tahun ?? '-');
            $sheet->setCellValue('D' . $row, $r->sekolah?->nama_sekolah ?? '-');
            $sheet->setCellValue('E' . $row, $r->jurusan?->nama_jurusan ?? '-');
            $sheet->setCellValue('F' . $row, $r->siswa?->nisn ?? '-');
            $sheet->setCellValue('G' . $row, $r->siswa?->nama_siswa ?? '-');
            $sheet->setCellValue('H' . $row, $r->siswa?->jk === 'laki_laki' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('I' . $row, $r->siswa?->asal_sekolah ?? '-');
            $sheet->setCellValue('J' . $row, $r->siswa?->tahun_lulus ?? '-');
            $sheet->setCellValue('K' . $row, ucfirst($r->jalur_pendaftaran ?? '-'));
            $sheet->setCellValue('L' . $row, $statusLabels[$r->status] ?? ucfirst($r->status ?? '-'));
            $sheet->setCellValue('M' . $row, $r->tanggal_submit?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('N' . $row, $r->userVerifikator?->name ?? '-');
            $sheet->setCellValue('O' . $row, ucfirst($r->dibuat_oleh ?? '-'));

            // Zebra striping
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':O' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0FDF4');
            }
            $row++;
        }

        // Border all data
        if ($row > 5) {
            $sheet->getStyle('A4:O' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']],
                ],
            ]);
        }

        // Auto width
        foreach (range('A', 'O') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'pendaftaran_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ============================================================
    //  PEMBAYARAN
    // ============================================================

    public function pembayaranPdf(Request $request)
    {
        $query = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.jurusan', 'metodePembayaran', 'verifikator'])
            ->whereHas('pendaftaran', fn($q) => $q->sekolah())
            ->orderBy('tanggal_pembayaran', 'desc');

        $this->applyPembayaranFilters($query, $request);

        $records     = $query->get();
        $dateFrom    = $request->date_from;
        $dateTo      = $request->date_to;
        $status      = $request->status;
        $sekolahNama = $request->sekolah_id ? Sekolah::find($request->sekolah_id)?->nama_sekolah : null;
        $jurusanNama = $request->jurusan_id ? \App\Models\Jurusan::find($request->jurusan_id)?->nama_jurusan : null;

        $html = view('exports.pembayaran-pdf', compact(
            'records', 'dateFrom', 'dateTo', 'status', 'sekolahNama', 'jurusanNama'
        ))->render();

        $dompdf = new \Dompdf\Dompdf([
            'enable_html5_parser'    => true,
            'enable_remote'          => false,
            'font_dir'               => storage_path('fonts/'),
            'font_cache'             => storage_path('fonts/'),
            'temp_dir'               => sys_get_temp_dir(),
            'chroot'                 => base_path(),
            'default_font'           => 'DejaVu Sans',
            'enable_font_subsetting' => true,
            'dpi'                    => 96,
        ]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'pembayaran_' . now()->format('Ymd_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function pembayaranExcel(Request $request)
    {
        $query = Pembayaran::with(['pendaftaran.siswa', 'pendaftaran.sekolah', 'pendaftaran.jurusan', 'metodePembayaran', 'verifikator'])
            ->whereHas('pendaftaran', fn($q) => $q->sekolah())
            ->orderBy('tanggal_pembayaran', 'desc');

        $this->applyPembayaranFilters($query, $request);
        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pembayaran');

        $sheet->setCellValue('A1', 'LAPORAN DATA PEMBAYARAN — PPDB Terpusat YPFC');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $info = 'Dicetak: ' . now()->format('d/m/Y H:i');
        if ($request->date_from || $request->date_to) {
            $info .= '   |   Periode: ' . ($request->date_from ?? '—') . ' s/d ' . ($request->date_to ?? '—');
        }
        if ($request->status)     $info .= '   |   Status: ' . ucfirst(str_replace('_', ' ', $request->status));
        if ($request->sekolah_id) $info .= '   |   Sekolah: ' . (Sekolah::find($request->sekolah_id)?->nama_sekolah ?? '-');

        $sheet->setCellValue('A2', $info);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A2')->getFont()->getColor()->setARGB('FF6B7280');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Summary row
        $totalNominal = $records->sum('nominal');
        $totalLunas   = $records->where('status_pembayaran', 'sukses')->sum('nominal');
        $sheet->setCellValue('A3', 'Total Transaksi: ' . $records->count() . '   |   Total Nominal: Rp ' . number_format($totalNominal, 0, ',', '.') . '   |   Total Diterima: Rp ' . number_format($totalLunas, 0, ',', '.'));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setSize(9)->setBold(true);
        $sheet->getStyle('A3')->getFont()->getColor()->setARGB('FF16A34A');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Headers
        $headers = ['No', 'Kode Registrasi', 'Nama Calon Murid', 'Sekolah', 'Jurusan', 'Metode', 'Nominal', 'Status', 'Tanggal Bayar', 'Diverifikasi Oleh', 'Catatan'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '5', $h);
            $col++;
        }

        $sheet->getStyle('A5:K5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(22);

        $statusLabels = [
            'pending'             => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'sukses'              => 'Lunas',
            'gagal'               => 'Gagal',
            'kadaluarsa'          => 'Kadaluarsa',
        ];

        $row = 7;
        foreach ($records as $i => $r) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $r->pendaftaran?->kode_regis ?? '-');
            $sheet->setCellValue('C' . $row, $r->pendaftaran?->siswa?->nama_siswa ?? '-');
            $sheet->setCellValue('D' . $row, $r->pendaftaran?->sekolah?->nama_sekolah ?? '-');
            $sheet->setCellValue('E' . $row, $r->pendaftaran?->jurusan?->nama_jurusan ?? '-');
            $sheet->setCellValue('F' . $row, ucfirst($r->metodePembayaran?->nama_metode ?? '-'));
            $sheet->setCellValue('G' . $row, $r->nominal);
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->setCellValue('H' . $row, $statusLabels[$r->status_pembayaran] ?? ucfirst($r->status_pembayaran ?? '-'));
            $sheet->setCellValue('I' . $row, $r->tanggal_pembayaran?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('J' . $row, $r->verifikator?->name ?? '-');
            $sheet->setCellValue('K' . $row, $r->catatan ?? '-');

            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0FDF4');
            }
            $row++;
        }

        if ($row > 6) {
            $sheet->getStyle('A5:K' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }

        foreach (range('A', 'K') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'pembayaran_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ============================================================
    //  LAPORAN REKAP (gabungan pendaftaran + pembayaran)
    // ============================================================

    public function laporanPdf(Request $request)
    {
        $query = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'tahunAkademik', 'pembayarans'])
            ->sekolah()
            ->orderBy('tanggal_submit', 'desc');

        $this->applyPendaftaranFilters($query, $request);

        $records     = $query->get();
        $dateFrom    = $request->date_from;
        $dateTo      = $request->date_to;
        $status      = $request->status;
        $sekolahNama = $request->sekolah_id ? Sekolah::find($request->sekolah_id)?->nama_sekolah : null;
        $jurusanNama = $request->jurusan_id ? Jurusan::find($request->jurusan_id)?->nama_jurusan : null;

        $html = view('exports.laporan-pdf', compact(
            'records', 'dateFrom', 'dateTo', 'status', 'sekolahNama', 'jurusanNama'
        ))->render();

        $dompdf = new \Dompdf\Dompdf([
            'enable_html5_parser'    => true,
            'enable_remote'          => false,
            'font_dir'               => storage_path('fonts/'),
            'font_cache'             => storage_path('fonts/'),
            'temp_dir'               => sys_get_temp_dir(),
            'chroot'                 => base_path(),
            'default_font'           => 'DejaVu Sans',
            'enable_font_subsetting' => true,
            'dpi'                    => 96,
        ]);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan_rekap_' . now()->format('Ymd_His') . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function laporanExcel(Request $request)
    {
        $query = Pendaftaran::with(['siswa', 'sekolah', 'jurusan', 'tahunAkademik', 'pembayarans'])
            ->sekolah()
            ->orderBy('tanggal_submit', 'desc');

        $this->applyPendaftaranFilters($query, $request);
        $records = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Rekap PPDB');

        // ─── Header utama
        $sheet->setCellValue('A1', 'LAPORAN REKAP PPDB — PPDB Terpusat Yayasan Fatahillah Cilegon');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $info = 'Dicetak: ' . now()->format('d/m/Y H:i');
        if ($request->date_from || $request->date_to)
            $info .= '   |   Periode: ' . ($request->date_from ?? '—') . ' s/d ' . ($request->date_to ?? '—');
        if ($request->status)
            $info .= '   |   Status: ' . ucfirst(str_replace('_', ' ', $request->status));
        if ($request->sekolah_id)
            $info .= '   |   Sekolah: ' . (Sekolah::find($request->sekolah_id)?->nama_sekolah ?? '-');

        $sheet->setCellValue('A2', $info);
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A2')->getFont()->getColor()->setARGB('FF6B7280');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ─── Summary table (row 3-4) - 9 kolom, semua status mutually exclusive
        $rDisetujui   = $records->where('status', 'diproses')->count();
        $rDiverif     = $records->where('status', 'diverifikasi')->count();
        $rDiterima    = $records->where('status', 'diterima')->count();
        $rDitolak     = $records->where('status', 'ditolak')->count();
        $rBlmBayar    = $records->where('status', 'menunggu_pembayaran')->count();
        $rProsesBayar = $records->where('status', 'pembayaran_diproses')->count();
        $rLunas       = $records->where('status', 'pembayaran_lunas')->count();
        $rSelesai     = $records->where('status', 'selesai')->count();

        $summaryHeaders = ['Total Pendaftar', 'Diproses', 'Diverifikasi', 'Diterima', 'Ditolak', 'Belum Bayar', 'Proses Bayar', 'Sudah Bayar', 'Selesai'];
        $summaryVals    = [$records->count(), $rDisetujui, $rDiverif, $rDiterima, $rDitolak, $rBlmBayar, $rProsesBayar, $rLunas, $rSelesai];
        $summaryCols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($summaryHeaders as $i => $sh) {
            $sheet->setCellValue($summaryCols[$i] . '3', $sh);
            $sheet->setCellValue($summaryCols[$i] . '4', $summaryVals[$i]);
        }

        // Style header summary row 3 - background hijau
        $sheet->getStyle('A3:I3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Style nilai row 4 - background hijau muda
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF6EE7B7']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(26);

        // Warna angka per kolom
        $sheet->getStyle('B4')->getFont()->getColor()->setARGB('FFD97706'); // diproses       - amber
        $sheet->getStyle('C4')->getFont()->getColor()->setARGB('FF2563EB'); // diverifikasi   - biru
        $sheet->getStyle('D4')->getFont()->getColor()->setARGB('FF16A34A'); // diterima       - hijau
        $sheet->getStyle('E4')->getFont()->getColor()->setARGB('FFDC2626'); // ditolak        - merah
        $sheet->getStyle('F4')->getFont()->getColor()->setARGB('FFD97706'); // belum bayar    - amber
        $sheet->getStyle('G4')->getFont()->getColor()->setARGB('FF2563EB'); // proses bayar   - biru
        $sheet->getStyle('H4')->getFont()->getColor()->setARGB('FF16A34A'); // sudah bayar    - hijau
        $sheet->getStyle('I4')->getFont()->getColor()->setARGB('FF0D9488'); // selesai        - teal

        // Baris 5 = kosong (spacer), header data di row 6

        // ─── Column headers (row 6, bukan row 5 lagi karena summary ada di 3-4)
        $headers = [
            'No', 'Kode Registrasi', 'Tahun Akademik', 'Nama Calon Siswa', 'NISN',
            'Jenis Kelamin', 'Asal Sekolah', 'Sekolah Tujuan', 'Jurusan',
            'Jalur Daftar', 'Tanggal Submit', 'Status Pendaftaran',
            'Status Pembayaran', 'Nominal Bayar', 'Verifikator', 'Dibuat Oleh',
        ];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '6', $h);
            $col++;
        }

        $sheet->getStyle('A6:P6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(22);

        $statusLabels = [
            'diproses'            => 'Diproses',
            'diverifikasi'        => 'Diverifikasi',
            'diterima'            => 'Diterima',
            'ditolak'             => 'Ditolak',
            'menunggu_pembayaran' => 'Menunggu Bayar',
            'pembayaran_diproses' => 'Bayar Diproses',
            'pembayaran_lunas'    => 'Bayar Lunas',
            'selesai'             => 'Selesai',
        ];
        $bayarLabels = [
            'sukses'              => 'Lunas',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'pending'             => 'Pending',
            'gagal'               => 'Gagal',
            'kadaluarsa'          => 'Kadaluarsa',
        ];

        $row = 7;
        foreach ($records as $i => $r) {
            $latestBayar = $r->pembayarans->sortByDesc('created_at')->first();

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $r->kode_regis);
            $sheet->setCellValue('C' . $row, $r->tahunAkademik?->tahun ?? '-');
            $sheet->setCellValue('D' . $row, $r->siswa?->nama_siswa ?? '-');
            $sheet->setCellValue('E' . $row, $r->siswa?->nisn ?? '-');
            $sheet->setCellValue('F' . $row, $r->siswa?->jk === 'laki_laki' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('G' . $row, $r->siswa?->asal_sekolah ?? '-');
            $sheet->setCellValue('H' . $row, $r->sekolah?->nama_sekolah ?? '-');
            $sheet->setCellValue('I' . $row, $r->jurusan?->nama_jurusan ?? '-');
            $sheet->setCellValue('J' . $row, ucfirst($r->jalur_pendaftaran ?? '-'));
            $sheet->setCellValue('K' . $row, $r->tanggal_submit?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('L' . $row, $statusLabels[$r->status] ?? ucfirst($r->status ?? '-'));
            $sheet->setCellValue('M' . $row, $latestBayar ? ($bayarLabels[$latestBayar->status_pembayaran] ?? ucfirst($latestBayar->status_pembayaran)) : 'Belum Bayar');
            $sheet->setCellValue('N' . $row, $latestBayar?->nominal ?? 0);
            $sheet->getStyle('N' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->setCellValue('O' . $row, $r->userVerifikator?->name ?? '-');
            $sheet->setCellValue('P' . $row, ucfirst($r->dibuat_oleh ?? '-'));

            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':P' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0FDF4');
            }
            $row++;
        }

        if ($row > 7) {
            $sheet->getStyle('A6:P' . ($row - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE5E7EB']]],
            ]);
        }

        foreach (range('A', 'P') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'laporan_rekap_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ============================================================
    //  HELPERS
    // ============================================================

    private function applyPendaftaranFilters($query, Request $request): void
    {
        if ($request->date_from) $query->whereDate('tanggal_submit', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('tanggal_submit', '<=', $request->date_to);
        if ($request->status)    $query->where('status', $request->status);
        if ($request->sekolah_id) $query->where('sekolah_id', $request->sekolah_id);
        if ($request->jurusan_id) $query->where('jurusan_id', $request->jurusan_id);
    }

    private function applyPembayaranFilters($query, Request $request): void
    {
        if ($request->date_from)  $query->whereDate('tanggal_pembayaran', '>=', $request->date_from);
        if ($request->date_to)    $query->whereDate('tanggal_pembayaran', '<=', $request->date_to);
        if ($request->status)     $query->where('status_pembayaran', $request->status);
        if ($request->sekolah_id) $query->whereHas('pendaftaran', fn($q) => $q->where('sekolah_id', $request->sekolah_id));
        if ($request->jurusan_id) $query->whereHas('pendaftaran', fn($q) => $q->where('jurusan_id', $request->jurusan_id));
    }
}

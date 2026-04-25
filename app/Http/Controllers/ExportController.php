<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiPesertaExport;
use App\Exports\AbsensiPanitiaExport;
use App\Exports\KesehatanExport;
use App\Exports\NotulensiExport; // Jangan lupa import
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportPeserta()
    {
        return Excel::download(
            new AbsensiPesertaExport(),
            'absensi-peserta-pkkmb-' . date('Ymd') . '.xlsx'
        );
    }

    public function exportPanitia()
    {
        return Excel::download(
            new AbsensiPanitiaExport(),
            'absensi-panitia-pkkmb-' . date('Ymd') . '.xlsx'
        );
    }
    public function exportKesehatan()
    {
        return Excel::download(new KesehatanExport, 'rekap-kesehatan-peserta.xlsx');
    }
    public function exportNotulensi($id)
    {
        $notulensi = \App\Models\Notulensi::findOrFail($id);
        $namaFile = 'Notulensi_' . str_replace(' ', '_', $notulensi->topik) . '_' . $notulensi->tanggal . '.xlsx';
        
        return Excel::download(new NotulensiExport($id), $namaFile);
    }
}
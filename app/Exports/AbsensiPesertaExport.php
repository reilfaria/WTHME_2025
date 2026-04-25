<?php

namespace App\Exports;

use App\Models\AbsensiPeserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class AbsensiPesertaExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];
        
        // Ambil semua kelompok yang ada
        $kelompoks = AbsensiPeserta::distinct()->pluck('kelompok')->sort();
        
        foreach ($kelompoks as $kelompok) {
            $sheets[] = new AbsensiPerKelompokSheet($kelompok);
        }
        
        // Sheet ringkasan
        $sheets[] = new AbsensiRingkasanSheet();
        
        return $sheets;
    }
}

class AbsensiPerKelompokSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    private $kelompok;
    
    public function __construct($kelompok)
    {
        $this->kelompok = $kelompok;
    }
    
    public function collection()
    {
        return AbsensiPeserta::where('kelompok', $this->kelompok)
            ->orderBy('nama')
            ->get()
            ->map(function ($row, $index) {
                return [
                    'No'             => $index + 1,
                    'Nama'           => $row->nama,
                    'NIM'            => $row->nim,
                    'Angkatan'       => $row->angkatan,
                    'Kelompok'       => $row->kelompok,
                    'Status'         => $row->status === 'hadir' ? 'HADIR' : 'TIDAK HADIR',
                    'Waktu Absen'    => $row->waktu_absen ? $row->waktu_absen->format('d/m/Y H:i') : '-',
                    'Sesi'           => $row->qrSession->nama_sesi ?? '-',
                ];
            });
    }
    
    public function headings(): array
    {
        return ['No', 'Nama', 'NIM', 'Angkatan', 'Kelompok', 'Status Kehadiran', 'Waktu Absen', 'Nama Sesi'];
    }
    
    public function title(): string
    {
        return 'Kelompok ' . $this->kelompok;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '002f45']],
            ],
        ];
    }
}

class AbsensiRingkasanSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return AbsensiPeserta::selectRaw('kelompok, COUNT(*) as total_hadir')
            ->where('status', 'hadir')
            ->groupBy('kelompok')
            ->orderBy('kelompok')
            ->get()
            ->map(fn($row) => [
                'Kelompok'    => $row->kelompok,
                'Total Hadir' => $row->total_hadir,
            ]);
    }
    
    public function headings(): array
    {
        return ['Kelompok', 'Total Hadir'];
    }
    
    public function title(): string
    {
        return 'Ringkasan';
    }
}
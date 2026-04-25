<?php

namespace App\Exports;

use App\Models\TugasKategori;
use App\Models\TugasPengumpulan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TugasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets    = [];
        $kelompoks = User::where('role', 'peserta')
            ->distinct()
            ->orderBy('kelompok')
            ->pluck('kelompok');

        foreach ($kelompoks as $kelompok) {
            $sheets[] = new TugasPerKelompokSheet($kelompok);
        }

        $sheets[] = new TugasRingkasanSheet();

        return $sheets;
    }
}

class TugasPerKelompokSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private $kelompok) {}

    public function collection()
    {
        $tugasList = TugasKategori::orderBy('urutan')->orderBy('created_at')->get();
        $peserta   = User::where('role', 'peserta')
            ->where('kelompok', $this->kelompok)
            ->orderBy('name')
            ->get();

        $pengumpulanMap = TugasPengumpulan::whereIn('user_id', $peserta->pluck('id'))
            ->get()
            ->groupBy('user_id')
            ->map(fn($items) => $items->keyBy('tugas_kategori_id'));

        return $peserta->map(function ($p, $i) use ($tugasList, $pengumpulanMap) {
            $row = [
                'No'       => $i + 1,
                'Nama'     => $p->name,
                'NIM'      => $p->nim,
                'Kelompok' => $p->kelompok,
            ];

            foreach ($tugasList as $tugas) {
                $kumpul = $pengumpulanMap[$p->id][$tugas->id] ?? null;
                if ($kumpul) {
                    $row[$tugas->nama_tugas] = strtoupper($kumpul->status) .
                        ' (' . $kumpul->dikumpulkan_at->format('d/m H:i') . ')';
                } else {
                    $row[$tugas->nama_tugas] = 'BELUM';
                }
            }

            return $row;
        });
    }

    public function headings(): array
    {
        $tugasList = TugasKategori::orderBy('urutan')->orderBy('created_at')->pluck('nama_tugas')->toArray();
        return array_merge(['No', 'Nama', 'NIM', 'Kelompok'], $tugasList);
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

class TugasRingkasanSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        $tugasList    = TugasKategori::orderBy('urutan')->get();
        $totalPeserta = User::where('role', 'peserta')->count();

        return $tugasList->map(function ($tugas, $i) use ($totalPeserta) {
            $sudah      = TugasPengumpulan::where('tugas_kategori_id', $tugas->id)->count();
            $terlambat  = TugasPengumpulan::where('tugas_kategori_id', $tugas->id)
                ->where('status', 'terlambat')->count();
            $belum      = $totalPeserta - $sudah;
            $pct        = $totalPeserta > 0 ? round(($sudah / $totalPeserta) * 100, 1) : 0;

            return [
                'No'              => $i + 1,
                'Nama Tugas'      => $tugas->nama_tugas,
                'Deadline'        => $tugas->deadline?->format('d/m/Y H:i') ?? '-',
                'Status'          => $tugas->aktif ? 'Aktif' : 'Nonaktif',
                'Sudah Kumpul'    => $sudah,
                'Tepat Waktu'     => $sudah - $terlambat,
                'Terlambat'       => $terlambat,
                'Belum Kumpul'    => $belum,
                '% Pengumpulan'   => $pct . '%',
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Tugas', 'Deadline', 'Status', 'Sudah Kumpul', 'Tepat Waktu', 'Terlambat', 'Belum Kumpul', '% Pengumpulan'];
    }

    public function title(): string { return 'Ringkasan Tugas'; }

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

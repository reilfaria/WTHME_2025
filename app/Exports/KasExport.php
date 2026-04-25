<?php

namespace App\Exports;

use App\Models\KasTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class KasExport implements WithMultipleSheets
{
    public function __construct(private array $filters = []) {}

    public function sheets(): array
    {
        return [
            new KasTransaksiSheet($this->filters),
            new KasRingkasanDivisiSheet(),
        ];
    }
}

class KasTransaksiSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        $query = KasTransaksi::with('pencatat')
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc');

        if (!empty($this->filters['jenis'])) {
            $query->where('jenis', $this->filters['jenis']);
        }
        if (!empty($this->filters['divisi'])) {
            $query->where('divisi', $this->filters['divisi']);
        }

        $rows    = $query->get();
        $saldo   = 0;
        $result  = collect();

        foreach ($rows as $i => $t) {
            $saldo += $t->jenis === 'masuk' ? $t->nominal : -$t->nominal;
            $result->push([
                'No'           => $i + 1,
                'Tanggal'      => $t->tanggal->format('d/m/Y'),
                'Jenis'        => strtoupper($t->jenis),
                'Nominal'      => $t->nominal,
                'Divisi'       => $t->divisi ?? '-',
                'Keterangan'   => $t->keterangan,
                'PIC'          => $t->pic,
                'Saldo'        => $saldo,
                'Dicatat Oleh' => $t->pencatat->name ?? '-',
                'Waktu Input'  => $t->created_at->format('d/m/Y H:i'),
            ]);
        }

        return $result;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Jenis', 'Nominal (Rp)', 'Divisi', 'Keterangan', 'PIC', 'Saldo (Rp)', 'Dicatat Oleh', 'Waktu Input'];
    }

    public function title(): string { return 'Semua Transaksi'; }

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

class KasRingkasanDivisiSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        $totalMasuk  = KasTransaksi::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = KasTransaksi::where('jenis', 'keluar')->sum('nominal');

        $divisi = KasTransaksi::where('jenis', 'keluar')
            ->selectRaw('divisi, SUM(nominal) as total, COUNT(*) as jumlah')
            ->groupBy('divisi')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($r) => [
                'Divisi'          => $r->divisi,
                'Jumlah Transaksi'=> $r->jumlah,
                'Total Pengeluaran'=> $r->total,
                '% dari Total'    => $totalKeluar > 0
                    ? round(($r->total / $totalKeluar) * 100, 1) . '%'
                    : '0%',
            ]);

        // Tambahkan baris total
        $divisi->push(['Divisi' => 'TOTAL MASUK', 'Jumlah Transaksi' => '', 'Total Pengeluaran' => $totalMasuk, '% dari Total' => '']);
        $divisi->push(['Divisi' => 'TOTAL KELUAR', 'Jumlah Transaksi' => '', 'Total Pengeluaran' => $totalKeluar, '% dari Total' => '']);
        $divisi->push(['Divisi' => 'SALDO AKHIR', 'Jumlah Transaksi' => '', 'Total Pengeluaran' => $totalMasuk - $totalKeluar, '% dari Total' => '']);

        return $divisi;
    }

    public function headings(): array
    {
        return ['Divisi', 'Jumlah Transaksi', 'Total Pengeluaran (Rp)', '% dari Total Keluar'];
    }

    public function title(): string { return 'Ringkasan per Divisi'; }

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

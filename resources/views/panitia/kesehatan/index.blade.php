@extends('layouts.app')

@section('content')
    <div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem; background-color: #e0decd;">
        <div style="max-width:1000px; margin:0 auto;">

            {{-- Header & Action Bar --}}
            <div
                style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1.5rem;">
                <div>
                    <a href="{{ route('panitia.index') }}"
                        style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:1.5rem;">
                        ← Kembali
                    </a>
                    <h1
                        style="font-family:'Playfair Display',serif; color:#002f45; font-size:2rem; font-weight:700; margin:0;">
                        Data Kesehatan Peserta
                    </h1>
                </div>

                <div style="display:flex; gap:0.75rem; align-items:center;">
                    {{-- Filter Form --}}
                    <form action="{{ route('panitia.kesehatan.index') }}" method="GET" style="display:flex; gap:0.5rem;">
                        <select name="kelompok"
                            style="padding:0.6rem; border-radius:0.6rem; border:2px solid #bdd1d3; font-size:0.875rem; background:white; color:#002f45; outline:none;">
                            <option value="">Semua Kelompok</option>
                            @foreach ($kelompokList as $k)
                                <option value="{{ $k }}" {{ request('kelompok') == $k ? 'selected' : '' }}>
                                    Kelompok {{ $k }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            style="background:#bdd1d3; color:#002f45; border:none; padding:0.6rem 1rem; border-radius:0.6rem; cursor:pointer; font-weight:700; font-size:0.875rem;">
                            Filter
                        </button>
                    </form>

                    {{-- Export Button --}}
                    <a href="{{ route('panitia.export.kesehatan') }}"
                        style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:700; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                        ⬇ Export Excel
                    </a>
                </div>
            </div>

            {{-- Main Content --}}
            @if ($semuaRiwayat->isEmpty())
                <div
                    style="background:white; border-radius:1rem; padding:4rem 2rem; text-align:center; border:2px solid #bdd1d3;">
                    <div style="font-size:4rem; margin-bottom:1rem;">🩺</div>
                    <h3 style="color:#002f45; margin-bottom:0.5rem;">Data Kosong</h3>
                    <p style="color:#002f45; opacity:0.5;">Belum ada peserta yang mengisi riwayat kesehatan.</p>
                </div>
            @else
                @foreach ($semuaRiwayat as $kelompok => $dataKesehatan)
                    <div
                        style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3; margin-bottom:2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div
                            style="background:#002f45; padding:1rem 1.5rem; display:flex; justify-content:space-between; align-items:center;">
                            <span style="color:#d2c296; font-weight:700; letter-spacing: 0.025em;">KELOMPOK
                                {{ $kelompok }}</span>
                            <span
                                style="color:white; background:rgba(255,255,255,0.1); padding:0.2rem 0.75rem; border-radius:2rem; font-size:0.75rem;">
                                {{ $dataKesehatan->count() }} Peserta
                            </span>
                        </div>

                        <div style="overflow-x: auto;">
                            <table style="width:100%; border-collapse:collapse; min-width: 800px;">
                                <thead>
                                    <tr style="background:#f9f8f6; border-bottom: 1px solid #e0decd;">
                                        <th
                                            style="padding:1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                            Nama & NIM</th>
                                        <th
                                            style="padding:1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                            Kondisi</th>
                                        <th
                                            style="padding:1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                            Riwayat & Alergi</th>
                                        <th
                                            style="padding:1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                            Obat Rutin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataKesehatan as $r)
                                        <tr style="border-bottom:1px solid #f1f1f1; transition: background 0.2s; {{ $r->kondisi_kesehatan == 'Perlu Perhatian' ? 'background:#fff5f5;' : '' }}"
                                            onmouseover="this.style.background='#fcfbf9'"
                                            onmouseout="this.style.background='{{ $r->kondisi_kesehatan == 'Perlu Perhatian' ? '#fff5f5' : 'transparent' }}'">
                                            <td style="padding:1rem;">
                                                <div style="color:#002f45; font-weight:600; font-size:0.9rem;">
                                                    {{ $r->nama }}</div>
                                                <div style="color:#002f45; font-size:0.75rem; opacity:0.6;">
                                                    {{ $r->nim }}</div>
                                            </td>
                                            <td style="padding:1rem;">
                                                @php
                                                    $color =
                                                        $r->kondisi_kesehatan == 'Perlu Perhatian'
                                                            ? '#c53030'
                                                            : ($r->kondisi_kesehatan == 'Cukup'
                                                                ? '#975a16'
                                                                : '#2f855a');
                                                    $bg =
                                                        $r->kondisi_kesehatan == 'Perlu Perhatian'
                                                            ? '#fed7d7'
                                                            : ($r->kondisi_kesehatan == 'Cukup'
                                                                ? '#fefcbf'
                                                                : '#c6f6d5');
                                                @endphp
                                                <span
                                                    style="background:{{ $bg }}; color:{{ $color }}; padding:0.3rem 0.75rem; border-radius:0.5rem; font-weight:800; font-size:0.7rem; display:inline-block; border: 1px solid {{ $color }}20;">
                                                    {{ strtoupper($r->kondisi_kesehatan) }}
                                                </span>
                                            </td>
                                            <td style="padding:1rem; color:#002f45; font-size:0.85rem; line-height:1.4;">
                                                <div style="margin-bottom:0.25rem;"><strong>Penyakit:</strong>
                                                    {{ $r->riwayat_penyakit ?? '-' }}</div>
                                                <div><strong>Alergi:</strong> {{ $r->alergi ?? '-' }}</div>
                                            </td>
                                            <td style="padding:1rem; color:#002f45; font-size:0.85rem;">
                                                <div style="font-weight: 500;">{{ $r->obat_rutin ?? '-' }}</div>
                                                @if ($r->keterangan_tambahan)
                                                    <div
                                                        style="font-size:0.75rem; color:#c53030; margin-top:0.4rem; padding:0.4rem; background:rgba(197,48,48,0.05); border-left:2px solid #c53030; border-radius: 0 4px 4px 0;">
                                                        <strong>Catatan:</strong> {{ $r->keterangan_tambahan }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
@endsection

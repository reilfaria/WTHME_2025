@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:1300px; margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('panitia.tugas.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">← Kembali ke Kelola Tugas</a>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">📊 Rekap Pengumpulan Tugas</h1>
            <p style="color:#002f45; opacity:0.5; font-size:0.875rem;">Status pengumpulan per peserta, dikelompokkan per kelompok</p>
        </div>
        <a href="{{ route('panitia.tugas.export') }}"
           style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:700;">
            ⬇ Export Excel
        </a>
    </div>

    {{-- Statistik per Tugas --}}
    @if($tugasList->isNotEmpty())
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:1rem; margin-bottom:2rem;">
        @foreach($tugasList as $tugas)
        @php $stat = $statsPerTugas[$tugas->id]; $total = \App\Models\User::where('role','peserta')->count(); @endphp
        <div style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3;">
            <div style="color:#002f45; font-size:0.75rem; font-weight:700; margin-bottom:0.5rem; line-height:1.3;">{{ $tugas->nama_tugas }}</div>
            <div style="display:flex; align-items:baseline; gap:0.35rem; margin-bottom:0.5rem;">
                <span style="color:#002f45; font-size:1.5rem; font-weight:800;">{{ $stat['sudah_kumpul'] }}</span>
                <span style="color:#002f45; opacity:0.4; font-size:0.75rem;">/ {{ $total }}</span>
            </div>
            {{-- Progress bar --}}
            <div style="background:#e0decd; border-radius:999px; height:5px; margin-bottom:0.4rem;">
                @php $pct = $total > 0 ? min(100, round(($stat['sudah_kumpul']/$total)*100)) : 0 @endphp
                <div style="background:#002f45; height:5px; border-radius:999px; width:{{ $pct }}%;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.7rem;">
                <span style="color:#166534;">✓ {{ $stat['sudah_kumpul'] - $stat['terlambat'] }} tepat</span>
                @if($stat['terlambat'] > 0)
                <span style="color:#991b1b;">⚠ {{ $stat['terlambat'] }} telat</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Filter Kelompok --}}
    <div style="background:white; border-radius:1rem; padding:1rem 1.25rem; border:2px solid #bdd1d3; margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('panitia.tugas.rekap') }}" style="display:flex; gap:0.75rem; align-items:flex-end; flex-wrap:wrap;">
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:600; color:#002f45; margin-bottom:0.3rem; text-transform:uppercase; letter-spacing:0.05em;">Kelompok</label>
                <select name="kelompok"
                    style="padding:0.6rem 1rem; border:2px solid #bdd1d3; border-radius:0.5rem; font-size:0.875rem; color:#002f45; background:white; outline:none;">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompokList as $k)
                    <option value="{{ $k }}" {{ $filterKelompok == $k ? 'selected' : '' }}>Kelompok {{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border:none; border-radius:0.5rem; cursor:pointer; font-weight:600; font-size:0.875rem;">Tampilkan</button>
            @if($filterKelompok)
            <a href="{{ route('panitia.tugas.rekap') }}" style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.5rem; text-decoration:none; font-weight:600; font-size:0.875rem;">Reset</a>
            @endif
        </form>
    </div>

    @if($tugasList->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <p style="color:#002f45; opacity:0.5;">Belum ada tugas yang dibuat. <a href="{{ route('panitia.tugas.index') }}" style="color:#002f45; font-weight:700;">Buat sekarang →</a></p>
    </div>
    @elseif($pesertaPerKelompok->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <p style="color:#002f45; opacity:0.5;">Belum ada peserta terdaftar.</p>
    </div>
    @else

    {{-- Tabel per Kelompok --}}
    @foreach($pesertaPerKelompok as $kelompok => $pesertaList)
    <div style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3; margin-bottom:1.75rem;">

        {{-- Header kelompok --}}
        <div style="background:#002f45; padding:0.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#d2c296; font-weight:700; font-size:0.9rem;">Kelompok {{ $kelompok }}</span>
            <span style="color:#bdd1d3; font-size:0.8rem;">{{ $pesertaList->count() }} peserta</span>
        </div>

        <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:{{ 280 + ($tugasList->count() * 150) }}px;">
            <thead>
                <tr style="background:#f9f8f6;">
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap; min-width:200px; border-bottom:2px solid #e0decd;">Nama / NIM</th>
                    @foreach($tugasList as $tugas)
                    <th style="padding:0.75rem 0.875rem; text-align:center; color:#002f45; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; border-bottom:2px solid #e0decd; border-left:1px solid #e0decd; min-width:150px;">
                        <div style="margin-bottom:0.2rem;">{{ $tugas->nama_tugas }}</div>
                        @if($tugas->deadline)
                        <div style="font-weight:400; font-size:0.65rem; color:{{ $tugas->isTerlambat() ? '#991b1b' : '#d97706' }}; text-transform:none; letter-spacing:0;">
                            {{ $tugas->isTerlambat() ? '⏰ Lewat' : '⏰' }} {{ $tugas->deadline->format('d/m H:i') }}
                        </div>
                        @endif
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($pesertaList as $i => $peserta)
                <tr style="border-bottom:1px solid #f0ede4; {{ $loop->even ? 'background:#fafaf8;' : '' }}">
                    {{-- Nama & NIM --}}
                    <td style="padding:0.75rem 1rem;">
                        <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ $peserta->name }}</div>
                        <div style="color:#002f45; opacity:0.4; font-size:0.75rem; font-family:monospace;">{{ $peserta->nim }}</div>
                    </td>

                    {{-- Status per Tugas --}}
                    @foreach($tugasList as $tugas)
                    @php $p = $pengumpulanMap[$peserta->id][$tugas->id] ?? null; @endphp
                    <td style="padding:0.75rem 0.875rem; text-align:center; border-left:1px solid #f0ede4; vertical-align:middle;">
                        @if($p)
                        <div style="display:flex; flex-direction:column; align-items:center; gap:0.3rem;">
                            {{-- Badge status --}}
                            <span style="display:inline-block; padding:0.15rem 0.5rem; border-radius:999px; font-size:0.65rem; font-weight:700;
                                {{ $p->status === 'tepat_waktu' ? 'background:#dcfce7; color:#166534;' : 'background:#fee2e2; color:#991b1b;' }}">
                                {{ $p->status === 'tepat_waktu' ? '✓ Tepat' : '⚠ Terlambat' }}
                            </span>
                            {{-- Waktu kumpul --}}
                            <span style="font-size:0.65rem; color:#002f45; opacity:0.5;">
                                {{ $p->dikumpulkan_at->format('d/m H:i') }}
                            </span>
                            {{-- Info file + download --}}
                            <div style="display:flex; align-items:center; gap:0.3rem;">
                                <span style="font-size:0.65rem; color:#002f45; opacity:0.5; font-family:monospace; text-transform:uppercase;">
                                    {{ strtoupper($p->file_ekstensi) }} · {{ $p->ukuranFormatted() }}
                                </span>
                                <a href="{{ route('panitia.tugas.download', $p->id) }}"
                                   style="display:inline-block; padding:0.15rem 0.5rem; background:#e0decd; color:#002f45; border-radius:0.3rem; text-decoration:none; font-size:0.65rem; font-weight:700;"
                                   title="Download {{ $p->file_nama_asli }}">⬇</a>
                            </div>
                            @if($p->catatan)
                            <span style="font-size:0.65rem; color:#002f45; opacity:0.5; font-style:italic; max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $p->catatan }}">
                                "{{ $p->catatan }}"
                            </span>
                            @endif
                        </div>
                        @else
                        <span style="display:inline-block; padding:0.2rem 0.6rem; background:#f3f4f6; color:#9ca3af; border-radius:999px; font-size:0.65rem; font-weight:600;">
                            — Belum
                        </span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#e0decd;">
                    <td style="padding:0.6rem 1rem; color:#002f45; font-size:0.75rem; font-weight:700;">Total Terkumpul</td>
                    @foreach($tugasList as $tugas)
                    @php
                        $kumpul = $pesertaList->filter(fn($p) =>
                            isset($pengumpulanMap[$p->id][$tugas->id])
                        )->count();
                    @endphp
                    <td style="padding:0.6rem 0.875rem; text-align:center; border-left:1px solid #bdd1d3; font-size:0.8rem; font-weight:700; color:#002f45;">
                        {{ $kumpul }} / {{ $pesertaList->count() }}
                    </td>
                    @endforeach
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
    @endforeach

    @endif

</div>
</div>
@endsection

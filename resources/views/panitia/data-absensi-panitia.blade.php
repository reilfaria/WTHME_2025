@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:1000px; margin:0 auto;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('panitia.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">← Kembali</a>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">
                Data Absensi Panitia
            </h1>
        </div>
        <a href="{{ route('panitia.export.panitia') }}"
           style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:700;">
            ⬇ Export Excel
        </a>
    </div>

    @if($absensi->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <div style="font-size:3rem; margin-bottom:1rem;">📊</div>
        <p style="color:#002f45; opacity:0.5;">Belum ada data absensi panitia.</p>
    </div>
    @else
    @foreach($absensi as $divisi => $dataDivisi)
    <div style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3; margin-bottom:1.5rem;">
        <div style="background:#002f45; padding:0.875rem 1.25rem; display:flex; justify-content:space-between; align-items:center;">
            <span style="color:#d2c296; font-weight:700;">Divisi {{ $divisi }}</span>
            <span style="color:#bdd1d3; font-size:0.8rem;">{{ $dataDivisi->count() }} orang hadir</span>
        </div>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f9f8f6;">
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; opacity:0.6;">No</th>
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; opacity:0.6;">Nama</th>
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; opacity:0.6;">NIM</th>
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; opacity:0.6;">Sesi</th>
                    <th style="padding:0.75rem 1rem; text-align:left; color:#002f45; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; opacity:0.6;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataDivisi as $i => $absen)
                <tr style="border-top:1px solid #e0decd;">
                    <td style="padding:0.75rem 1rem; color:#002f45; opacity:0.4; font-size:0.875rem;">{{ $i + 1 }}</td>
                    <td style="padding:0.75rem 1rem; color:#002f45; font-weight:600; font-size:0.875rem;">{{ $absen->nama }}</td>
                    <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.875rem; opacity:0.7;">{{ $absen->nim }}</td>
                    <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.75rem; opacity:0.6;">{{ $absen->qrSession->nama_sesi ?? '-' }}</td>
                    <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.75rem; opacity:0.6;">{{ $absen->waktu_absen->format('d/m H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
    @endif

</div>
</div>
@endsection

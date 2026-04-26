@extends('layouts.app')

@section('content')
    <div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem; background-color:#f4f3ee;">
        <div style="max-width:1400px; margin:0 auto;">

            {{-- Header Section --}}
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3rem; border-bottom:3px solid #002f45; padding-bottom:1.5rem;">
                <div>
                    <a href="{{ route('panitia.index') }}"
                        style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">←
                        Kembali</a>
                    <h1
                        style="font-family:'Playfair Display',serif; color:#002f45; font-size:2.5rem; font-weight:800; margin:0;">
                        📋 Rekapitulasi</h1>
                    <p style="color:#002f45; opacity:0.6; margin-top:0.5rem; font-size:1.1rem;">Klasifikasi berdasarkan
                        Kelompok & Jenis Kegiatan</p>
                </div>
                <a href="{{ route('panitia.mentoring.export_seluruh') }}"
                    style="padding:0.8rem 1.5rem; background:#70ad47; color:white; border-radius:0.75rem; text-decoration:none; font-weight:700; border:2px solid #002f45; box-shadow: 4px 4px 0px #002f45;">
                    📥 Download Excel
                </a>
            </div>

            @foreach ($rekapDetail as $noKelompok => $perKegiatan)
                <div style="margin-bottom:4rem;">
                    {{-- JUDUL KELOMPOK BESAR --}}
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                        <div
                            style="width:50px; height:50px; background:#002f45; color:#d2c296; display:flex; align-items:center; justify-content:center; border-radius:12px; font-weight:800; font-size:1.4rem;">
                            {{ $noKelompok }}
                        </div>
                        <h2 style="font-family:'Playfair Display',serif; color:#002f45; margin:0; font-size:1.8rem;">
                            KELOMPOK {{ $noKelompok }}</h2>
                        <div
                            style="flex-grow:1; height:2px; background:repeating-linear-gradient(to right, #002f45, #002f45 5px, transparent 5px, transparent 10px); opacity:0.2;">
                        </div>
                    </div>

                    {{-- LOOP PER KEGIATAN --}}
                    <div
                        style="display:grid; grid-template-columns: 1fr; gap:2rem; padding-left:1.5rem; border-left:4px solid #bdd1d3;">
                        @foreach ($perKegiatan as $namaKegiatan => $details)
                            <div
                                style="background:white; border:2px solid #002f45; border-radius:1rem; overflow:hidden; box-shadow: 6px 6px 0px #002f45;">

                                {{-- Sub-Header Kegiatan --}}
                                <div
                                    style="padding:1rem 1.5rem; background:#f9f8f6; border-bottom:2px solid #002f45; display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-weight:800; color:#002f45; font-size:1rem;">📌
                                        {{ strtoupper($namaKegiatan) }}</span>
                                    <span
                                        style="font-size:0.85rem; color:#666; background:#e0decd; padding:0.2rem 0.8rem; border-radius:20px;">
                                        📅 {{ date('d M Y', strtotime($details->first()->mentoring->tanggal)) }}
                                    </span>
                                </div>

                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="background:#fff; border-bottom:1px solid #eee;">
                                            <th
                                                style="padding:0.8rem 1.5rem; text-align:left; color:#888; font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                                                Nama Peserta</th>
                                            <th
                                                style="padding:0.8rem 1.5rem; text-align:left; color:#888; font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                                                NIM</th>
                                            <th
                                                style="padding:0.8rem 1.5rem; text-align:center; color:#888; font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                                                Status</th>
                                            <th
                                                style="padding:0.8rem 1.5rem; text-align:left; color:#888; font-size:0.7rem; text-transform:uppercase; letter-spacing:1px;">
                                                Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($details as $rd)
                                            <tr style="border-bottom:1px solid #f4f3ee;">
                                                <td style="padding:0.8rem 1.5rem; font-weight:700; color:#002f45;">
                                                    {{ $rd->peserta->name }}</td>
                                                <td style="padding:0.8rem 1.5rem; color:#666; font-size:0.85rem;">
                                                    {{ $rd->peserta->nim }}</td>
                                                <td style="padding:0.8rem 1.5rem; text-align:center;">
                                                    <span
                                                        style="display:inline-block; padding:0.3rem 0.7rem; border-radius:0.5rem; font-size:0.7rem; font-weight:800;
                                        {{ $rd->kehadiran === 'Hadir' ? 'background:#dcfce7; color:#166534;' : ($rd->kehadiran === 'Izin' ? 'background:#fff7ed; color:#9a3412;' : 'background:#fee2e2; color:#991b1b;') }}">
                                                        {{ strtoupper($rd->kehadiran) }}
                                                    </span>
                                                </td>
                                                <td
                                                    style="padding:0.8rem 1.5rem; color:#999; font-size:0.8rem; font-style:italic;">
                                                    {{ $rd->keterangan ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Footer Kecil per Kegiatan --}}
                                <div
                                    style="padding:0.7rem 1.5rem; background:#fff; border-top:1px solid #eee; display:flex; gap:1.5rem; font-size:0.8rem;">
                                    <span style="color:#166534;">✅ Hadir:
                                        <strong>{{ $details->where('kehadiran', 'Hadir')->count() }}</strong></span>
                                    <span style="color:#991b1b;">❌ Tidak Hadir:
                                        <strong>{{ $details->whereIn('kehadiran', ['Izin', 'Alpha'])->count() }}</strong></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
{{-- Main Background with Modern Glassmorphism --}}
<div style="min-height:calc(100vh - 64px); padding:3rem 1.5rem; background: linear-gradient(135deg, #e0decd 0%, #bdd1d3 100%); font-family: 'Inter', sans-serif;">
    <div style="max-width:1100px; margin:0 auto;">

        {{-- Header & Action Bar --}}
        <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:2.5rem; flex-wrap:wrap; gap:1.5rem;">
            <div>
                <a href="{{ route('panitia.index') }}" 
                   style="color:#002f45; opacity:0.7; text-decoration:none; font-size:0.9rem; display:inline-flex; align-items:center; margin-bottom:1rem; transition:0.3s; font-weight:600;"
                   onmouseover="this.style.opacity='1'; this.style.transform='translateX(-5px)'"
                   onmouseout="this.style.opacity='0.7'; this.style.transform='translateX(0)'">
                   <span style="margin-right:8px;">←</span> Kembali ke Dashboard
                </a>
                <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:2.5rem; font-weight:800; margin:0; letter-spacing:-0.02em;">
                    Absensi <span style="color:#6b705c; font-style:italic;">Peserta</span>
                </h1>
            </div>
            
            <a href="{{ route('panitia.export.peserta') }}"
               style="padding:0.8rem 1.5rem; background: rgba(0, 47, 69, 0.9); color:#d2c296; border-radius:1rem; text-decoration:none; font-size:0.875rem; font-weight:700; backdrop-filter: blur(10px); transition:0.3s; display:flex; align-items:center; gap:10px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);"
               onmouseover="this.style.background='#002f45'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='rgba(0, 47, 69, 0.9)'; this.style.transform='translateY(0)'">
                <span style="font-size: 1.1rem;">⬇</span> Export Excel
            </a>
        </div>

        @if($absensi->isEmpty())
        <div style="background: rgba(255, 255, 255, 0.3); backdrop-filter: blur(20px); border-radius: 2rem; padding: 5rem 2rem; text-align: center; border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
            <div style="font-size:5rem; margin-bottom:1.5rem; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));">📋</div>
            <h3 style="color:#002f45; font-size:1.5rem; margin-bottom:0.5rem; font-weight:700;">Belum Ada Data</h3>
            <p style="color:#002f45; opacity:0.6; max-width:400px; margin:0 auto;">Data kehadiran peserta belum tercatat di sistem untuk saat ini.</p>
        </div>
        @else
        
        @foreach($absensi as $kelompok => $dataKelompok)
        <div style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); border-radius: 1.5rem; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.4); margin-bottom: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.05);">
            
            {{-- Kelompok Header --}}
            <div style="background: rgba(0, 47, 69, 0.85); padding: 1.25rem 2rem; display: flex; justify-content: space-between; align-items: center; backdrop-filter: blur(5px);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:12px; height:12px; background:#d2c296; border-radius:3px; transform: rotate(45deg);"></div>
                    <span style="color:#d2c296; font-weight:800; letter-spacing: 0.1em; font-size:0.9rem; text-transform:uppercase;">KELOMPOK {{ $kelompok }}</span>
                </div>
                <span style="color:white; background:rgba(255,255,255,0.15); padding:0.4rem 1rem; border-radius:2rem; font-size:0.8rem; font-weight:600; border: 1px solid rgba(255,255,255,0.2);">
                    {{ $dataKelompok->count() }} Peserta Hadir
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background: rgba(255, 255, 255, 0.1);">
                            <th style="padding:1.25rem 1.5rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; border-bottom: 1px solid rgba(0,0,0,0.05);">No</th>
                            <th style="padding:1.25rem 1.5rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; border-bottom: 1px solid rgba(0,0,0,0.05);">Identitas Peserta</th>
                            <th style="padding:1.25rem 1.5rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; border-bottom: 1px solid rgba(0,0,0,0.05);">Angkatan</th>
                            <th style="padding:1.25rem 1.5rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; border-bottom: 1px solid rgba(0,0,0,0.05);">Sesi</th>
                            <th style="padding:1.25rem 1.5rem; text-align:left; color:#002f45; font-size:0.7rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; border-bottom: 1px solid rgba(0,0,0,0.05);">Waktu Presensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataKelompok as $i => $absen)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.03); transition: 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.4)'"
                            onmouseout="this.style.background='transparent'">
                            <td style="padding:1.25rem 1.5rem; color:#002f45; opacity:0.5; font-family:monospace; font-weight:600;">{{ sprintf('%02d', $i + 1) }}</td>
                            <td style="padding:1.25rem 1.5rem;">
                                <div style="color:#002f45; font-weight:700; font-size:0.95rem;">{{ $absen->nama }}</div>
                                <div style="color:#002f45; font-size:0.8rem; opacity:0.6; font-family:monospace; margin-top:2px;">{{ $absen->nim }}</div>
                            </td>
                            <td style="padding:1.25rem 1.5rem;">
                                <div style="color:#002f45; font-weight:600; font-size:0.85rem; opacity:0.8;">{{ $absen->angkatan }}</div>
                            </td>
                            <td style="padding:1.25rem 1.5rem;">
                                <span style="background: rgba(0, 47, 69, 0.05); color: #002f45; padding: 0.4rem 0.8rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(0, 47, 69, 0.1);">
                                    {{ $absen->qrSession->nama_sesi ?? '-' }}
                                </span>
                            </td>
                            <td style="padding:1.25rem 1.5rem;">
                                <div style="color:#002f45; font-size:0.8rem; font-weight:600;">
                                    <span style="opacity:0.5; margin-right:4px;">📅</span> {{ $absen->waktu_absen->format('d M') }}
                                    <span style="margin-left:10px; opacity:0.5; margin-right:4px;">⏰</span> {{ $absen->waktu_absen->format('H:i') }}
                                </div>
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

<style>
    /* Custom Scrollbar for better aesthetics */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.05); }
    ::-webkit-scrollbar-thumb { background: rgba(0, 47, 69, 0.2); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(0, 47, 69, 0.4); }
</style>
@endsection
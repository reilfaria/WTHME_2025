@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:700px; margin:0 auto;">

    <div style="margin-bottom:2rem;">
        <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">
            Portal Peserta
        </h1>
        <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-top:0.25rem;">
            Halo, {{ auth()->user()->name }} — Kelompok {{ auth()->user()->kelompok }}
        </p>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

        {{-- Absensi --}}
        <a href="{{ route('peserta.absen') }}"
           style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.5rem;
                  transition:all 0.2s;"
           onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
            <div style="font-size:2rem; margin-bottom:0.75rem;">📷</div>
            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:0.25rem;">Absensi Kegiatan</h3>
            <p style="color:#002f45; opacity:0.5; font-size:0.8rem;">Scan QR Code untuk konfirmasi kehadiran</p>
        </a>

        {{-- Tugas --}}
        <a href="{{ route('peserta.tugas') }}"
           style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.5rem;
                  transition:all 0.2s; position:relative;"
           onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
            @if($tugasBelum > 0)
            <span style="position:absolute; top:1rem; right:1rem; background:#ef4444; color:white;
                         font-size:0.65rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:999px; min-width:20px; text-align:center;">
                {{ $tugasBelum }}
            </span>
            @endif
            <div style="font-size:2rem; margin-bottom:0.75rem;">📚</div>
            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:0.25rem;">Pengumpulan Tugas</h3>
            <p style="color:#002f45; opacity:0.5; font-size:0.8rem;">
                @if($tugasBelum > 0)
                    <span style="color:#ef4444; font-weight:600;">{{ $tugasBelum }} tugas</span> belum dikumpulkan
                @elseif($tugasAktif > 0)
                    Semua tugas sudah dikumpulkan ✓
                @else
                    Upload file tugas yang diberikan panitia
                @endif
            </p>
        </a>

        {{-- Riwayat Penyakit --}}
        <a href="{{ route('peserta.riwayat') }}"
           style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.5rem;
                  transition:all 0.2s; position:relative;"
           onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
            @if(!$sudahIsiRiwayat)
            <span style="position:absolute; top:1rem; right:1rem; background:#f59e0b; color:white;
                         font-size:0.65rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:999px;">
                Belum diisi
            </span>
            @endif
            <div style="font-size:2rem; margin-bottom:0.75rem;">🏥</div>
            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:0.25rem;">Riwayat Kesehatan</h3>
            <p style="color:#002f45; opacity:0.5; font-size:0.8rem;">Isi formulir riwayat penyakit & kondisi kesehatan</p>
        </a>

    </div>

    {{-- Info identitas --}}
    <div style="margin-top:1.5rem; background:white; border-radius:1rem; padding:1.25rem 1.5rem;
                border:1px solid #bdd1d3; display:flex; gap:2rem; flex-wrap:wrap;">
        <div>
            <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">NIM</div>
            <div style="color:#002f45; font-weight:600;">{{ auth()->user()->nim }}</div>
        </div>
        <div>
            <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Angkatan</div>
            <div style="color:#002f45; font-weight:600;">{{ auth()->user()->angkatan }}</div>
        </div>
        <div>
            <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Kelompok</div>
            <div style="color:#002f45; font-weight:600;">{{ auth()->user()->kelompok }}</div>
        </div>
    </div>

</div>
</div>
@endsection
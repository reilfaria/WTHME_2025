@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:900px; margin:0 auto;">

    <div style="margin-bottom:2rem;">
        <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">
            Portal Panitia
        </h1>
        <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-top:0.25rem;">
            Halo, {{ auth()->user()->name }}
            @if(auth()->user()->divisi)
            — Divisi {{ auth()->user()->divisi }}
            @endif
            @if(auth()->user()->role === 'bendahara')
            <span style="background:#d2c296; color:#002f45; font-size:0.7rem; font-weight:700; padding:0.15rem 0.6rem; border-radius:999px; margin-left:0.5rem;">BENDAHARA</span>
            @endif
        </p>
    </div>

    {{-- Statistik --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem;">
        <div style="background:#002f45; border-radius:1rem; padding:1.25rem;">
            <div style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Peserta Hadir</div>
            <div style="color:#d2c296; font-size:2rem; font-weight:800;">{{ $totalPesertaHadir }}</div>
        </div>
        <div style="background:#002f45; border-radius:1rem; padding:1.25rem;">
            <div style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Panitia Hadir</div>
            <div style="color:#d2c296; font-size:2rem; font-weight:800;">{{ $totalPanitiaHadir }}</div>
        </div>
        <div style="background:#002f45; border-radius:1rem; padding:1.25rem;">
            <div style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">QR Aktif</div>
            <div style="color:#d2c296; font-size:2rem; font-weight:800;">{{ $qrSessions->where('aktif', true)->count() }}</div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

        {{-- Kolom kiri: Menu --}}
        <div style="display:flex; flex-direction:column; gap:1rem;">

            {{-- Absen Panitia --}}
            <a href="{{ route('panitia.absen') }}"
               style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                      display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
               onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">📷</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Absen Saya</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Scan QR untuk absen sebagai panitia</div>
                </div>
            </a>

            {{-- Generate QR --}}
            <a href="{{ route('panitia.qr.create') }}"
               style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                      display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
               onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">🔲</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Generate QR Absensi</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Buat QR baru untuk sesi kegiatan</div>
                </div>
            </a>

            {{-- Data Absensi Peserta --}}
            <a href="{{ route('panitia.absensi.peserta') }}"
               style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                      display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
               onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">📋</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Data Absensi Peserta</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Lihat & export rekap kehadiran peserta</div>
                </div>
            </a>

            {{-- Data Absensi Panitia --}}
            <a href="{{ route('panitia.absensi.panitia') }}"
               style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                      display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
               onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">📊</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Data Absensi Panitia</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Lihat & export rekap kehadiran panitia</div>
                </div>
            </a>
            {{-- Data Kesehatan Peserta --}}
            <a href="{{ route('panitia.kesehatan.index') }}"
            style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                    display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
            onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
            onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">🩺</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Data Kesehatan Peserta</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Pantau riwayat penyakit & alergi peserta</div>
                </div>
            </a>
            {{-- Kelola Tugas Peserta --}}
            <a href="{{ route('panitia.tugas.index') }}"
            style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                    display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
            onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
            onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">📚</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Kelola Tugas Peserta</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Buat tugas & cek pengumpulan file peserta</div>
                </div>
            </a>
            {{-- Notulensi Rapat --}}
            <a href="{{ route('panitia.notulensi.index') }}"
                style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem;
                        display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">✍️</div>
                <div>
                    <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Notulensi Rapat</div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Catat hasil rapat & poin pembahasan divisi</div>
                </div>
            </a>

            {{-- Kas Bendahara (hanya untuk bendahara dan admin) --}}
            @if(auth()->user()->isBendahara())
            <a href="{{ route('panitia.kas.index') }}"
               style="text-decoration:none; background:#002f45; border:2px solid #002f45; border-radius:1rem; padding:1.25rem;
                      display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
               onmouseover="this.style.opacity='0.85'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)'">
                <div style="width:44px; height:44px; background:rgba(210,194,150,0.2); border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem; border:1px solid rgba(210,194,150,0.3);">📒</div>
                <div>
                    <div style="color:#d2c296; font-weight:700; font-size:0.9rem;">Kas Event</div>
                    <div style="color:#bdd1d3; opacity:0.8; font-size:0.75rem;">Catat pemasukan & pengeluaran kas</div>
                </div>
                <span style="margin-left:auto; background:rgba(210,194,150,0.2); color:#d2c296; font-size:0.65rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:999px; border:1px solid rgba(210,194,150,0.3);">BENDAHARA</span>
            </a>
            @endif
        </div>

        {{-- Kolom kanan: Link Drive + QR aktif --}}
        <div style="display:flex; flex-direction:column; gap:1rem;">

            {{-- Link Resources --}}
            <div style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3;">
                <div style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">Dokumen Event</div>
                @forelse($links as $link)
                <a href="{{ $link->url }}" target="_blank"
                   style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border-radius:0.6rem;
                          text-decoration:none; transition:background 0.15s; margin-bottom:0.5rem;"
                   onmouseover="this.style.background='#e0decd'" onmouseout="this.style.background='transparent'">
                    <span style="font-size:1.25rem;">
                        @if($link->ikon === 'folder') 📁
                        @elseif($link->ikon === 'chart-bar') 📊
                        @elseif($link->ikon === 'currency-dollar') 💰
                        @else 🔗
                        @endif
                    </span>
                    <div>
                        <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ $link->nama }}</div>
                        <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">Klik untuk buka</div>
                    </div>
                    <span style="margin-left:auto; color:#002f45; opacity:0.3; font-size:0.75rem;">↗</span>
                </a>
                @empty
                <p style="color:#002f45; opacity:0.4; font-size:0.8rem; text-align:center; padding:1rem 0;">
                    Belum ada link. Tambahkan lewat database.
                </p>
                @endforelse
            </div>

            {{-- QR Session Aktif --}}
            @if($qrSessions->count() > 0)
            <div style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3;">
                <div style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">QR Sesi Terakhir</div>
                @foreach($qrSessions->take(3) as $qr)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #e0decd;">
                    <div>
                        <div style="color:#002f45; font-size:0.825rem; font-weight:600;">{{ $qr->nama_sesi }}</div>
                        <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">{{ ucfirst($qr->untuk) }} · {{ $qr->session_code }}</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <span style="width:8px; height:8px; border-radius:50%; background:{{ $qr->aktif ? '#16a34a' : '#d1d5db' }};"></span>
                        <a href="{{ route('panitia.qr.show', $qr->session_code) }}"
                           style="font-size:0.7rem; color:#002f45; opacity:0.6; text-decoration:none; font-weight:600;">
                            Lihat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>

</div>
</div>
@endsection
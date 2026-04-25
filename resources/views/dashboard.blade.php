@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:3rem 1.5rem;">
    <div style="max-width:800px; margin:0 auto;">

        <div style="text-align:center; margin-bottom:3rem;">
            <p style="color:#002f45; opacity:0.5; font-size:0.875rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">
                Selamat datang,
            </p>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:2.25rem; font-weight:700;">
                {{ auth()->user()->name }}
            </h1>
            <p style="color:#002f45; opacity:0.5; margin-top:0.5rem;">
                Pilih portal yang sesuai dengan peranmu
            </p>
        </div>

        <div style="display:grid; grid-template-columns:(3,1fr);; gap:1.5rem;">
            {{-- Card Admin --}}
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.index') }}" 
        style="text-decoration:none; display:block; background:#d2c296; border-radius:1.5rem; padding:2.5rem; 
                transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;"
        onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(210,194,150,0.4)'"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">

            <div style="width:56px; height:56px; background:rgba(0,47,69,0.1); border-radius:1rem; 
                        display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                ⚙️
            </div>

            <h3 style="color:#002f45; font-size:1.4rem; font-weight:700; margin-bottom:0.5rem; font-family:'Playfair Display',serif;">
                Panel Admin
            </h3>

            <p style="color:#002f45; font-size:0.85rem; opacity:0.7; line-height:1.6;">
                Kelola panitia, import Excel, dan manajemen akun
            </p>

            <div style="margin-top:1.5rem; display:flex; align-items:center; color:#002f45; font-size:0.85rem; font-weight:600;">
                Masuk ke Panel
                <span style="margin-left:0.5rem;">→</span>
            </div>
        </a>
        @endif

            {{-- Card Panitia --}}
            @if(auth()->user()->isPanitia())
            <a href="{{ route('panitia.index') }}" 
               style="text-decoration:none; display:block; background:#002f45; border-radius:1.5rem; padding:2.5rem; 
                      transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;"
               onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(0,47,69,0.3)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div style="width:56px; height:56px; background:rgba(210,194,150,0.2); border-radius:1rem; 
                            display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem; border:1px solid rgba(210,194,150,0.3);">
                    <svg style="width:28px; height:28px; color:#d2c296;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 style="color:#d2c296; font-size:1.4rem; font-weight:700; margin-bottom:0.5rem; font-family:'Playfair Display',serif;">
                    Portal Panitia
                </h3>
                <p style="color:#bdd1d3; font-size:0.85rem; opacity:0.8; line-height:1.6;">
                    Kelola absensi, lihat data peserta, akses dokumen event, dan generate QR Code
                </p>
                <div style="margin-top:1.5rem; display:flex; align-items:center; color:#d2c296; font-size:0.85rem; font-weight:600;">
                    Masuk ke Portal
                    <svg style="width:16px; height:16px; margin-left:0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>
            @else
            <div style="background:#002f45; border-radius:1.5rem; padding:2.5rem; opacity:0.4; cursor:not-allowed;">
                <div style="width:56px; height:56px; background:rgba(210,194,150,0.2); border-radius:1rem; 
                            display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                    <svg style="width:28px; height:28px; color:#d2c296;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 style="color:#d2c296; font-size:1.4rem; font-weight:700; margin-bottom:0.5rem; font-family:'Playfair Display',serif;">
                    Portal Panitia
                </h3>
                <p style="color:#bdd1d3; font-size:0.85rem; opacity:0.8;">Akses terbatas untuk panitia</p>
            </div>
            @endif

            {{-- Card Peserta --}}
            @if(auth()->user()->isPeserta())
            <a href="{{ route('peserta.index') }}"
               style="text-decoration:none; display:block; background:white; border-radius:1.5rem; padding:2.5rem; 
                      border:2px solid #bdd1d3; transition:transform 0.2s, box-shadow 0.2s, border-color 0.2s;"
               onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 20px 40px rgba(0,47,69,0.15)'; this.style.borderColor='#002f45'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='#bdd1d3'">
                <div style="width:56px; height:56px; background:#e0decd; border-radius:1rem; 
                            display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                    <svg style="width:28px; height:28px; color:#002f45;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 style="color:#002f45; font-size:1.4rem; font-weight:700; margin-bottom:0.5rem; font-family:'Playfair Display',serif;">
                    Portal Peserta
                </h3>
                <p style="color:#002f45; font-size:0.85rem; opacity:0.6; line-height:1.6;">
                    Lakukan absensi kegiatan, isi formulir kesehatan, dan akses informasi PKKMB
                </p>
                <div style="margin-top:1.5rem; display:flex; align-items:center; color:#002f45; font-size:0.85rem; font-weight:600;">
                    Masuk ke Portal
                    <svg style="width:16px; height:16px; margin-left:0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </a>
            @else
            <div style="background:white; border-radius:1.5rem; padding:2.5rem; border:2px solid #bdd1d3; opacity:0.4; cursor:not-allowed;">
                <div style="width:56px; height:56px; background:#e0decd; border-radius:1rem; 
                            display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                    <svg style="width:28px; height:28px; color:#002f45;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 style="color:#002f45; font-size:1.4rem; font-weight:700; margin-bottom:0.5rem; font-family:'Playfair Display',serif;">
                    Portal Peserta
                </h3>
                <p style="color:#002f45; font-size:0.85rem; opacity:0.6;">Akses terbatas untuk peserta</p>
            </div>
            @endif

        </div>

        {{-- Info identitas --}}
        <div style="margin-top:2rem; background:white; border-radius:1rem; padding:1.25rem 1.5rem; 
                    border:1px solid #bdd1d3; display:flex; gap:2rem; flex-wrap:wrap;">
            <div>
                <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">NIM</div>
                <div style="color:#002f45; font-weight:600; font-size:0.9rem;">{{ auth()->user()->nim ?? '-' }}</div>
            </div>
            <div>
                <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Angkatan</div>
                <div style="color:#002f45; font-weight:600; font-size:0.9rem;">{{ auth()->user()->angkatan ?? '-' }}</div>
            </div>
            @if(auth()->user()->kelompok)
            <div>
                <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Kelompok</div>
                <div style="color:#002f45; font-weight:600; font-size:0.9rem;">{{ auth()->user()->kelompok }}</div>
            </div>
            @endif
            @if(auth()->user()->divisi)
            <div>
                <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Divisi</div>
                <div style="color:#002f45; font-weight:600; font-size:0.9rem;">{{ auth()->user()->divisi }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
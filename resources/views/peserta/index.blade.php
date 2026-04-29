@extends('layouts.app')

@section('content')
    {{-- Background Wrapper dengan gradien lembut agar efek glass terlihat --}}
    <div
        style="min-height:calc(100vh - 64px); padding:3rem 1.5rem; background: linear-gradient(135deg, #e0decd 0%, #bdd1d3 100%);">
        <div style="max-width:800px; margin:0 auto;">

            {{-- Header --}}
            <div style="margin-bottom:2.5rem; text-align: center;">
                <h1
                    style="font-family:'Playfair Display',serif; color:#002f45; font-size:2.5rem; font-weight:800; margin:0; letter-spacing:-0.02em;">
                    Portal <span style="color:#6b705c; font-style:italic;">Peserta</span>
                </h1>
                <div
                    style="display: inline-block; padding: 0.5rem 1.5rem; background: rgba(255,255,255,0.3); backdrop-filter: blur(10px); border-radius: 2rem; border: 1px solid rgba(255,255,255,0.4);">
                    <p style="color:#002f45; font-size:0.95rem; margin:0; font-weight: 500;">
                        Selamat Datang, <span style="font-weight: 700;">{{ auth()->user()->name }}</span> — Kelompok
                        {{ auth()->user()->kelompok }}
                    </p>
                </div>
            </div>

            @foreach ($pengumuman as $info)
                <div style="background: rgba(255, 255, 255, 0.4); 
                backdrop-filter: blur(10px); 
                -webkit-backdrop-filter: blur(10px); 
                padding: 25px; 
                border-radius: 20px; 
                border: 1px solid rgba(255, 255, 255, 0.6); 
                margin-bottom: 20px; 
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s ease;
                animation: fadeInUp 0.5s ease-out forwards;"
                    onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">

                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            {{-- Badge Kategori --}}
                            <span
                                style="font-size: 0.65rem; 
                             background: #002f45; 
                             color: white; 
                             padding: 4px 12px; 
                             border-radius: 50px; 
                             font-weight: 800; 
                             letter-spacing: 0.5px;
                             display: inline-block;
                             margin-bottom: 10px;">
                                {{ strtoupper($info->kategori) }}
                            </span>

                            {{-- Judul --}}
                            <h4
                                style="margin: 0 0 12px 0; color: #002f45; font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700;">
                                {{ $info->judul }}
                            </h4>

                            {{-- Konten Teks --}}
                            @if ($info->konten)
                                <p
                                    style="color: #002f45; opacity: 0.8; font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px; font-family: 'Inter', sans-serif;">
                                    {!! nl2br(e($info->konten)) !!}
                                </p>
                            @endif

                            {{-- Tombol Link --}}
                            @if ($info->url_link)
                                <a href="{{ $info->url_link }}" target="_blank"
                                    style="display: inline-flex; 
                              align-items: center; 
                              gap: 8px;
                              background: #002f45; 
                              color: white; 
                              text-decoration: none; 
                              padding: 10px 18px; 
                              border-radius: 12px; 
                              font-size: 0.85rem; 
                              font-weight: 600;
                              transition: 0.3s;
                              box-shadow: 0 4px 15px rgba(0, 47, 69, 0.2);"
                                    onmouseover="this.style.background='#d2c296'; this.style.color='#002f45';"
                                    onmouseout="this.style.background='#002f45'; this.style.color='white';">
                                    Buka Tautan
                                    <span style="font-size: 1rem;">↗</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Grid Menu --}}
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">

                {{-- Card Template Function --}}
                @php
                    $menus = [
                        [
                            'route' => route('peserta.absen'),
                            'icon' => '📷',
                            'title' => 'Absensi Kegiatan',
                            'desc' => 'Scan QR Code untuk konfirmasi kehadiran',
                            'badge' => null,
                        ],
                        [
                            'route' => route('peserta.tugas'),
                            'icon' => '📚',
                            'title' => 'Pengumpulan Tugas',
                            'desc' =>
                                $tugasBelum > 0
                                    ? $tugasBelum . ' tugas belum dikumpulkan'
                                    : 'Semua tugas sudah dikumpulkan ✓',
                            'badge' => $tugasBelum > 0 ? ['val' => $tugasBelum, 'color' => '#ef4444'] : null,
                        ],
                        [
                            'route' => route('peserta.barang'),
                            'icon' => '📦',
                            'title' => 'Pengumpulan Barang',
                            'desc' => 'Input & update barang bawaan kelompok',
                            'badge' => null,
                        ],
                        [
                            'route' => route('peserta.riwayat'),
                            'icon' => '🏥',
                            'title' => 'Riwayat Kesehatan',
                            'desc' => 'Isi formulir kondisi kesehatan Anda',
                            'badge' => !$sudahIsiRiwayat ? ['val' => 'Wajib', 'color' => '#f59e0b'] : null,
                        ],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    <a href="{{ $menu['route'] }}"
                        style="text-decoration:none; background: rgba(255, 255, 255, 0.25); 
                           backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                           border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 1.5rem; 
                           padding: 1.75rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                           display: flex; align-items: center; gap: 1.25rem; position: relative;
                           box-shadow: 0 8px 32px 0 rgba(0, 47, 69, 0.05);"
                        onmouseover="this.style.background='rgba(255, 255, 255, 0.4)'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 40px 0 rgba(0, 47, 69, 0.1)';"
                        onmouseout="this.style.background='rgba(255, 255, 255, 0.25)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px 0 rgba(0, 47, 69, 0.05)';">
                        @if ($menu['badge'])
                            <span
                                style="position:absolute; top:1rem; right:1.25rem; background:{{ $menu['badge']['color'] }}; color:white;
                                     font-size:0.7rem; font-weight:700; padding:0.25rem 0.75rem; border-radius:999px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                {{ $menu['badge']['val'] }}
                            </span>
                        @endif

                        <div
                            style="font-size: 2.5rem; background: rgba(255,255,255,0.5); width: 60px; height: 60px; 
                                display: flex; align-items: center; justify-content: center; border-radius: 1rem;">
                            {{ $menu['icon'] }}
                        </div>

                        <div>
                            <h3 style="color:#002f45; font-weight:700; font-size:1.1rem; margin:0 0 0.25rem 0;">
                                {{ $menu['title'] }}</h3>
                            <p style="color:#002f45; opacity:0.6; font-size:0.85rem; margin:0; line-height:1.4;">
                                {{ $menu['desc'] }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Info Identitas (Glass Style) --}}
            <div
                style="margin-top:2.5rem; background: rgba(0, 47, 69, 0.05); backdrop-filter: blur(10px); 
                        border-radius: 1.5rem; padding: 1.5rem 2rem; border: 1px solid rgba(0, 47, 69, 0.1);
                        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">

                <div style="display: flex; gap: 3rem;">
                    <div>
                        <div
                            style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">
                            NIM</div>
                        <div style="color:#002f45; font-weight:700; font-size:1rem;">{{ auth()->user()->nim }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">
                            Angkatan</div>
                        <div style="color:#002f45; font-weight:700; font-size:1rem;">{{ auth()->user()->angkatan }}</div>
                    </div>
                    <div>
                        <div
                            style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.25rem;">
                            Kelompok</div>
                        <div style="color:#002f45; font-weight:700; font-size:1rem;">{{ auth()->user()->kelompok }}</div>
                    </div>
                </div>

                <div style="color: #002f45; opacity: 0.8; font-style: italic; font-size: 0.85rem;">
                    ElektroJoss
                </div>
            </div>



        </div>
    </div>
@endsection

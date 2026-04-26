@extends('layouts.app')

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Kontainer Statistik */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        /* Kontainer Menu & Sidebar */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        /* Penyesuaian untuk HP (Lebar < 768px) */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                /* Statistik jadi menumpuk 1 kolom */
            }

            .main-layout {
                grid-template-columns: 1fr;
                /* Menu & Sidebar jadi menumpuk */
            }

            .portal-title {
                font-size: 1.5rem !important;
                text-align: center;
            }

            .portal-subtitle {
                text-align: center;
                margin-bottom: 1.5rem;
            }
        }
    </style>

    <div style="min-height:calc(100vh - 64px); padding:1.5rem; background:#e0decd; font-family: 'Inter', sans-serif;">
        <div style="max-width:900px; margin:0 auto;">

            <div style="margin-bottom:2rem;">
                <h1 class="portal-title"
                    style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700; margin:0;">
                    Portal Panitia
                </h1>
                <p class="portal-subtitle" style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-top:0.25rem;">
                    Halo, {{ auth()->user()->name }}
                    @if (auth()->user()->divisi)
                        — Divisi {{ auth()->user()->divisi }}
                    @endif
                    @if (in_array($roleName, ['bendahara', 'korlap', 'ketuplak', 'admin']))
                        <span
                            style="background:#d2c296; color:#002f45; font-size:0.7rem; font-weight:700; padding:0.15rem 0.6rem; border-radius:999px; margin-left:0.5rem;">
                            {{ $badgeText }}
                        </span>
                    @endif
                </p>
            </div>

            {{-- Statistik - Sekarang Responsif --}}
            <div class="stats-grid">
                <div
                    style="background:#002f45; border-radius:1rem; padding:1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div
                        style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">
                        Peserta Hadir</div>
                    <div style="color:#d2c296; font-size:2rem; font-weight:800;">{{ $totalPesertaHadir }}</div>
                </div>
                <div
                    style="background:#002f45; border-radius:1rem; padding:1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div
                        style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">
                        Panitia Hadir</div>
                    <div style="color:#d2c296; font-size:2rem; font-weight:800;">{{ $totalPanitiaHadir }}</div>
                </div>
                <div
                    style="background:#002f45; border-radius:1rem; padding:1.25rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div
                        style="color:#bdd1d3; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">
                        QR Aktif</div>
                    <div style="color:#d2c296; font-size:2rem; font-weight:800;">
                        {{ $qrSessions->where('aktif', true)->count() }}</div>
                </div>
            </div>

            {{-- Layout Utama - Sekarang Responsif --}}
            <div class="main-layout">

                {{-- Kolom Kiri: Menu --}}
                <div style="display:flex; flex-direction:column; gap:1rem;">

                    {{-- Template Menu Item (Re-usable) --}}
                    @php
                        $menus = [
                            [
                                'route' => 'panitia.gantt.index',
                                'icon' => '📅',
                                'title' => 'Timeline & Gantt Chart',
                                'desc' => 'Pantau jadwal kegiatan dan planning event',
                                'role' => 'korlap',
                            ],
                            [
                                'route' => 'panitia.absen',
                                'icon' => '📷',
                                'title' => 'Absen Saya',
                                'desc' => 'Scan QR untuk absen sebagai panitia',
                            ],
                            [
                                'route' => 'panitia.qr.create',
                                'icon' => '🔲',
                                'title' => 'Generate QR Absensi',
                                'desc' => 'Buat QR baru untuk sesi kegiatan',
                            ],
                            [
                                'route' => 'panitia.absensi.peserta',
                                'icon' => '📋',
                                'title' => 'Data Absensi Peserta',
                                'desc' => 'Lihat & export rekap kehadiran peserta',
                            ],
                            [
                                'route' => 'panitia.absensi.panitia',
                                'icon' => '📊',
                                'title' => 'Data Absensi Panitia',
                                'desc' => 'Lihat & export rekap kehadiran panitia',
                            ],
                            [
                                'route' => 'panitia.kesehatan.index',
                                'icon' => '🩺',
                                'title' => 'Data Kesehatan Peserta',
                                'desc' => 'Pantau riwayat penyakit & alergi peserta',
                            ],
                            [
                                'route' => 'panitia.tugas.index',
                                'icon' => '📚',
                                'title' => 'Kelola Tugas Peserta',
                                'desc' => 'Buat tugas & cek pengumpulan file',
                            ],
                            [
                                'route' => 'panitia.notulensi.index',
                                'icon' => '✍️',
                                'title' => 'Notulensi Rapat',
                                'desc' => 'Catat hasil rapat & poin pembahasan',
                            ],
                        ];
                    @endphp

                    @foreach ($menus as $menu)
                        <a href="{{ route($menu['route']) }}"
                            style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:1rem; transition:all 0.2s;"
                            onmouseover="this.style.borderColor='#002f45'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.borderColor='#bdd1d3'; this.style.transform='translateY(0)'">
                            <div
                                style="width:44px; height:44px; background:#e0decd; border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem;">
                                {{ $menu['icon'] }}
                            </div>
                            <div>
                                <div style="color:#002f45; font-weight:700; font-size:0.9rem;">{{ $menu['title'] }}</div>
                                <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">{{ $menu['desc'] }}</div>
                            </div>
                            @if (isset($menu['role']) && auth()->user()->role === $menu['role'])
                                <span
                                    style="margin-left:auto; background:rgba(0,47,69,0.1); color:#002f45; font-size:0.6rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:999px; border:1px solid #bdd1d3;">{{ strtoupper($menu['role']) }}</span>
                            @endif
                        </a>
                    @endforeach

                    {{-- Mentoring Special --}}
                    @if (auth()->user()->isMentor())
                        <a href="{{ route('panitia.mentoring.index') }}"
                            style="text-decoration:none; background:white; border:2px solid #bdd1d3; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:1rem; transition:all 0.2s;">
                            <div
                                style="width:44px;height:44px;background:#e0decd;border-radius:0.75rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.25rem;">
                                👨‍🏫</div>
                            <div>
                                <div style="color:#002f45; font-weight:700; font-size:0.9rem;">Mentoring</div>
                                <div style="color:#002f45; opacity:0.5; font-size:0.75rem;">Catatan kegiatan & kehadiran
                                    kelompok</div>
                            </div>
                        </a>
                    @endif

                    {{-- Kas Bendahara Special --}}
                    @if (auth()->user()->isBendahara())
                        <a href="{{ route('panitia.kas.index') }}"
                            style="text-decoration:none; background:#002f45; border:2px solid #002f45; border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:1rem; transition:all 0.2s;">
                            <div
                                style="width:44px; height:44px; background:rgba(210,194,150,0.2); border-radius:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1.25rem; border:1px solid rgba(210,194,150,0.3);">
                                📒</div>
                            <div>
                                <div style="color:#d2c296; font-weight:700; font-size:0.9rem;">Kas Event</div>
                                <div style="color:#bdd1d3; opacity:0.8; font-size:0.75rem;">Catat pemasukan & pengeluaran
                                    kas</div>
                            </div>
                            <span
                                style="margin-left:auto; background:rgba(210,194,150,0.2); color:#d2c296; font-size:0.65rem; font-weight:700; padding:0.2rem 0.5rem; border-radius:999px;">BENDAHARA</span>
                        </a>
                    @endif
                </div>

                {{-- Kolom Kanan: Sidebar --}}
                <div style="display:flex; flex-direction:column; gap:1.5rem;">

                    {{-- Link Resources --}}
                    <div
                        style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3; height: fit-content;">
                        <div
                            style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">
                            Dokumen Event</div>
                        <a href="https://drive.google.com/drive/folders/1vKqGVSX3BQ4u5kRDBqSoetDs5byQzSF2?usp=sharing"
                            target="_blank"
                            style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border-radius:0.6rem; text-decoration:none; transition:background 0.15s; margin-bottom:0.5rem; background: rgba(0,47,69,0.05);"
                            onmouseover="this.style.background='#f0f0eb'"
                            onmouseout="this.style.background='rgba(0,47,69,0.05)'">
                            <span style="font-size:1.25rem;">📂</span>
                            <div>
                                <div style="color:#002f45; font-weight:600; font-size:0.875rem;">Folder Utama WTHME 2025
                                </div>
                                <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">Google Drive ↗</div>
                            </div>
                        </a>
                        @forelse($links as $link)
                            <a href="{{ $link->url }}" target="_blank"
                                style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border-radius:0.6rem; text-decoration:none; transition:background 0.15s; margin-bottom:0.5rem;"
                                onmouseover="this.style.background='#f0f0eb'"
                                onmouseout="this.style.background='transparent'">
                                <span style="font-size:1.25rem;">
                                    @if ($link->ikon === 'folder')
                                        📁
                                    @elseif($link->ikon === 'chart-bar')
                                        📊
                                    @elseif($link->ikon === 'currency-dollar')
                                        💰
                                    @else
                                        🔗
                                    @endif
                                </span>
                                <div>
                                    <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ $link->nama }}
                                    </div>
                                    <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">Klik untuk buka ↗</div>
                                </div>
                            </a>
                        @empty
                        @endforelse
                    </div>

                    {{-- QR Session Aktif --}}
                    @if ($qrSessions->count() > 0)
                        <div style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3;">
                            <div
                                style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">
                                QR Sesi Terakhir</div>
                            @foreach ($qrSessions->take(3) as $qr)
                                <div
                                    style="display:flex; align-items:center; justify-content:space-between; padding:0.8rem 0; border-bottom:1px solid #f0f0eb;">
                                    <div>
                                        <div style="color:#002f45; font-size:0.825rem; font-weight:600;">
                                            {{ $qr->nama_sesi }}</div>
                                        <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">
                                            {{ ucfirst($qr->untuk) }} · {{ $qr->session_code }}</div>
                                    </div>
                                    <a href="{{ route('panitia.qr.show', $qr->session_code) }}"
                                        style="font-size:0.7rem; background:#002f45; color:white; text-decoration:none; padding:0.3rem 0.7rem; border-radius:0.5rem; font-weight:600;">Lihat</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

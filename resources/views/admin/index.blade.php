@extends('layouts.app')

@section('content')
<div style="max-width:1100px; margin:0 auto; padding:2rem 1.5rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:gap:1rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">
                Manajemen Panitia
            </h1>
            <p style="color:#002f45; opacity:0.5; font-size:0.875rem;">Total: {{ $totalPanitia }} panitia terdaftar</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="{{ route('admin.template') }}"
               style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.6rem; 
                      text-decoration:none; font-size:0.875rem; font-weight:600; border:2px solid #bdd1d3;">
                ⬇ Download Template
            </a>
            <a href="{{ route('admin.import') }}"
               style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; 
                      text-decoration:none; font-size:0.875rem; font-weight:700;">
                ⬆ Import Excel
            </a>
        </div>
    </div>

    {{-- Hasil import --}}
    @if(session('imported'))
    <div style="background:#dcfce7; border:1px solid #86efac; border-radius:0.75rem; padding:1rem; margin-bottom:1rem;">
        <p style="color:#166534; font-weight:600; margin-bottom:0.5rem;">✅ Berhasil diimport:</p>
        @foreach(session('imported') as $item)
            <p style="color:#166534; font-size:0.8rem;">• {{ $item }}</p>
        @endforeach
    </div>
    @endif

    @if(session('skipped'))
    <div style="background:#fef9c3; border:1px solid #fde047; border-radius:0.75rem; padding:1rem; margin-bottom:1rem;">
        <p style="color:#854d0e; font-weight:600; margin-bottom:0.5rem;">⚠ Dilewati (sudah terdaftar):</p>
        @foreach(session('skipped') as $item)
            <p style="color:#854d0e; font-size:0.8rem;">• {{ $item }}</p>
        @endforeach
    </div>
    @endif

    {{-- Tabel Panitia --}}
    <div style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#002f45;">
                    <th style="padding:0.875rem 1rem; text-align:left; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Nama</th>
                    <th style="padding:0.875rem 1rem; text-align:left; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">NIM</th>
                    <th style="padding:0.875rem 1rem; text-align:left; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Angkatan</th>
                    <th style="padding:0.875rem 1rem; text-align:left; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Divisi</th>
                    <th style="padding:0.875rem 1rem; text-align:left; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Status PW</th>
                    <th style="padding:0.875rem 1rem; text-align:center; color:#d2c296; font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($panitiaList as $p)
                <tr style="border-bottom:1px solid #e0decd; {{ $loop->even ? 'background:#f9f8f6;' : '' }}">
                    <td style="padding:0.875rem 1rem; color:#002f45; font-weight:600; font-size:0.875rem;">{{ $p->name }}</td>
                    <td style="padding:0.875rem 1rem; color:#002f45; font-size:0.875rem; opacity:0.7;">{{ $p->nim }}</td>
                    <td style="padding:0.875rem 1rem; color:#002f45; font-size:0.875rem; opacity:0.7;">{{ $p->angkatan }}</td>
                    <td style="padding:0.875rem 1rem;">
                        <span style="background:#e0decd; color:#002f45; padding:0.25rem 0.75rem; border-radius:999px; font-size:0.75rem; font-weight:600;">
                            {{ $p->divisi }}
                        </span>
                    </td>
                    <td style="padding:0.875rem 1rem;">
                        @if($p->must_change_password)
                        <span style="color:#d97706; font-size:0.75rem; font-weight:600;">⚠ Belum ganti</span>
                        @else
                        <span style="color:#16a34a; font-size:0.75rem; font-weight:600;">✓ Sudah ganti</span>
                        @endif
                    </td>
                    <td style="padding:0.875rem 1rem; text-align:center;">
                        <div style="display:flex; gap:0.5rem; justify-content:center; flex-wrap:wrap;">
                            <a href="{{ route('admin.panitia.edit', $p->id) }}"
                               style="padding:0.35rem 0.75rem; background:#bdd1d3; color:#002f45; border-radius:0.4rem; text-decoration:none; font-size:0.75rem; font-weight:600;">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.panitia.reset', $p->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" onclick="return confirm('Reset password {{ $p->name }} ke NIM?')"
                                    style="padding:0.35rem 0.75rem; background:#d2c296; color:#002f45; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                                    Reset PW
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.panitia.delete', $p->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus akun {{ $p->name }}? Tindakan ini tidak bisa dibatalkan.')"
                                    style="padding:0.35rem 0.75rem; background:#fee2e2; color:#991b1b; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:3rem; text-align:center; color:#002f45; opacity:0.4;">
                        Belum ada data panitia. Silakan import via Excel.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
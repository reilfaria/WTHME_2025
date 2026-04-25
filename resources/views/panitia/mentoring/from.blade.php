@extends('layouts.app')

@section('content')
    <div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem; background-color:#f9f8f6;">
        <div style="max-width:1200px; margin:0 auto;">

            {{-- Header --}}
            <div
                style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
                <div>
                    <a href="{{ route('panitia.index') }}"
                        style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">←
                        Kembali ke Portal Panitia</a>
                    <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">📒
                        Mentoring Kelompok</h1>
                    <p style="color:#002f45; opacity:0.5; font-size:0.875rem;">Pencatatan kehadiran dan perkembangan peserta
                        mentoring</p>
                </div>
                @if (isset($kelompok))
                    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
                        <a href="{{ route('panitia.mentoring.index') }}"
                            style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:600; border:2px solid #bdd1d3;">
                            ← Ganti Kelompok
                        </a>
                        <a href="{{ route('panitia.mentoring.export', $kelompok) }}"
                            style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:700;">
                            ⬇ Export Rekap Excel
                        </a>
                    </div>
                @endif
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div
                    style="padding:0.875rem 1rem; background:#dcfce7; border:1px solid #86efac; border-radius:0.75rem; color:#166534; margin-bottom:1.5rem; font-size:0.875rem;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            {{-- KONDISI 1: JIKA KELOMPOK SUDAH DIPILIH --}}
            @if (isset($kelompok))
                {{-- Form Input --}}
                <div
                    style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3; margin-bottom:2rem;">
                    <h3
                        style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                        📝 Catat Pertemuan Baru - Kelompok {{ $kelompok }}
                    </h3>

                    <form method="POST" action="{{ route('panitia.mentoring.store', $kelompok) }}">
                        @csrf
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">
                            <div>
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Nama
                                    Kegiatan/Materi *</label>
                                <input type="text" name="nama_kegiatan" required placeholder="Misal: Adab Penuntut Ilmu"
                                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; outline:none;">
                            </div>
                            <div>
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Tanggal
                                    *</label>
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; outline:none;">
                            </div>
                        </div>

                        <div style="background:#f9f8f6; border-radius:1rem; padding:1.25rem; border:1px solid #e0decd;">
                            <div
                                style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem; font-weight:700;">
                                Presensi Anggota</div>

                            <div style="overflow-x:auto;">
                                <table style="width:100%; border-collapse:collapse;">
                                    <thead>
                                        <tr style="border-bottom:2px solid #bdd1d3;">
                                            <th style="padding:0.75rem; text-align:left; color:#002f45; font-size:0.75rem;">
                                                NAMA PESERTA</th>
                                            <th style="padding:0.75rem; text-align:left; color:#002f45; font-size:0.75rem;">
                                                NIM</th>
                                            <th
                                                style="padding:0.75rem; text-align:left; color:#002f45; font-size:0.75rem; width:180px;">
                                                KEHADIRAN</th>
                                            <th style="padding:0.75rem; text-align:left; color:#002f45; font-size:0.75rem;">
                                                CATATAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($peserta as $p)
                                            <tr style="border-bottom:1px solid #e0decd;">
                                                <td
                                                    style="padding:1rem 0.75rem; color:#002f45; font-weight:600; font-size:0.875rem;">
                                                    {{ $p->name }}</td>
                                                <td
                                                    style="padding:1rem 0.75rem; color:#002f45; opacity:0.6; font-size:0.8rem;">
                                                    {{ $p->nim }}</td>
                                                <td style="padding:1rem 0.75rem;">
                                                    <select name="kehadiran[{{ $p->id }}]" required
                                                        style="width:100%; padding:0.5rem; border:2px solid #bdd1d3; border-radius:0.5rem; font-size:0.8rem; background:white;">
                                                        <option value="Hadir">Hadir</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Alpha">Alpha</option>
                                                    </select>
                                                </td>
                                                <td style="padding:1rem 0.75rem;">
                                                    <input type="text" name="keterangan[{{ $p->id }}]"
                                                        placeholder="Opsional..."
                                                        style="width:100%; padding:0.5rem; border:1px solid #bdd1d3; border-radius:0.5rem; font-size:0.8rem;">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top:1.5rem; display:flex; justify-content:flex-end;">
                            <button type="submit"
                                style="padding:0.875rem 2.5rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.95rem;">
                                💾 Simpan Laporan Mentoring
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Riwayat Tabel --}}
                {{-- Riwayat Tabel --}}
                <h3
                    style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.5rem; margin-top:3rem; margin-bottom:1.5rem;">
                    📜 Riwayat Detail Pertemuan</h3>

                @forelse($mentorings as $men)
                    <div style="margin-bottom:2.5rem;">
                        {{-- Nama Kegiatan & Aksi Global --}}
                        <div
                            style="background:#e0decd; padding:0.75rem 1.25rem; border-radius:0.75rem 0.75rem 0 0; border:2px solid #bdd1d3; border-bottom:none; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-weight:700; color:#002f45; font-size:1rem;">📌
                                    {{ $men->nama_kegiatan }}</span>
                                <span
                                    style="color:#002f45; opacity:0.6; font-size:0.875rem; font-weight:600; margin-left:1rem;">📅
                                    {{ date('d F Y', strtotime($men->tanggal)) }}</span>
                            </div>
                            {{-- Tombol Hapus Seluruh Kegiatan --}}
                            <form method="POST" action="{{ route('panitia.mentoring.destroy', $men->id) }}"
                                onsubmit="return confirm('Hapus seluruh catatan kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="background:none; border:none; color:#991b1b; cursor:pointer; font-size:0.8rem; font-weight:700; display:flex; align-items:center; gap:0.25rem;">
                                    🗑️ Hapus Kegiatan
                                </button>
                            </form>
                        </div>

                        <div
                            style="background:white; border-radius:0 0 1rem 1rem; overflow:hidden; border:2px solid #bdd1d3;">
                            <div style="overflow-x:auto;">
                                <table style="width:100%; border-collapse:collapse; min-width:900px;">
                                    <thead>
                                        <tr style="background:#002f45;">
                                            <th
                                                style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                Nama</th>
                                            <th
                                                style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                NIM</th>
                                            <th
                                                style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                Gender</th>
                                            <th
                                                style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                Kehadiran</th>
                                            <th
                                                style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                Catatan</th>
                                            <th
                                                style="padding:0.75rem 1rem; text-align:center; color:#d2c296; font-size:0.7rem; text-transform:uppercase;">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($men->details as $det)
                                            <tr
                                                style="border-bottom:1px solid #e0decd; background:{{ $loop->even ? '#f9f8f6' : 'white' }};">
                                                <td
                                                    style="padding:0.875rem 1rem; color:#002f45; font-weight:600; font-size:0.875rem;">
                                                    {{ $det->peserta->name ?? 'User Terhapus' }}</td>
                                                <td
                                                    style="padding:0.875rem 1rem; color:#002f45; opacity:0.7; font-size:0.8rem;">
                                                    {{ $det->peserta->nim ?? '-' }}</td>
                                                <td style="padding:0.875rem 1rem; color:#002f45; font-size:0.8rem;">
                                                    {{ $det->peserta->gender ?? '-' }}</td>
                                                <td style="padding:0.875rem 1rem;">
                                                    <span id="status-text-{{ $det->id }}"
                                                        style="display:inline-block; padding:0.25rem 0.6rem; border-radius:0.5rem; font-size:0.7rem; font-weight:800;
                                        {{ $det->kehadiran === 'Hadir' ? 'background:#dcfce7; color:#166534;' : ($det->kehadiran === 'Izin' ? 'background:#fff7ed; color:#9a3412;' : 'background:#fee2e2; color:#991b1b;') }}">
                                                        {{ strtoupper($det->kehadiran) }}
                                                    </span>
                                                </td>
                                                <td
                                                    style="padding:0.875rem 1rem; color:#002f45; font-size:0.8rem; font-style:italic; opacity:0.8;">
                                                    {{ $det->keterangan ?? '—' }}
                                                </td>
                                                <td style="padding:0.875rem 1rem; text-align:center;">
                                                    <button
                                                        onclick="openEditModal('{{ $det->id }}', '{{ $det->kehadiran }}', '{{ $det->keterangan }}')"
                                                        style="background:#e0decd; border:1px solid #bdd1d3; border-radius:0.4rem; padding:0.3rem 0.6rem; cursor:pointer; font-size:0.75rem; color:#002f45; font-weight:700;">
                                                        ✏️ Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px dashed #bdd1d3;">
                        <p style="color:#002f45; opacity:0.5;">Belum ada riwayat mentoring untuk kelompok ini.</p>
                    </div>
                @endforelse

                {{-- MODAL EDIT SEDERHANA (Taruh di paling bawah sebelum @endsection) --}}
                <div id="editModal"
                    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,47,69,0.8); z-index:9999; align-items:center; justify-content:center;">
                    <div
                        style="background:white; padding:2rem; border-radius:1.25rem; width:100%; max-width:400px; border:2px solid #d2c296;">
                        <h3 style="font-family:'Playfair Display',serif; color:#002f45; margin-bottom:1.5rem;">Update
                            Kehadiran</h3>
                        <form id="editForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <div style="margin-bottom:1rem;">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:700; color:#002f45; margin-bottom:0.5rem;">STATUS
                                    KEHADIRAN</label>
                                <select name="kehadiran" id="modalKehadiran"
                                    style="width:100%; padding:0.75rem; border:2px solid #bdd1d3; border-radius:0.6rem;">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpha">Alpha</option>
                                </select>
                            </div>
                            <div style="margin-bottom:1.5rem;">
                                <label
                                    style="display:block; font-size:0.75rem; font-weight:700; color:#002f45; margin-bottom:0.5rem;">CATATAN</label>
                                <input type="text" name="keterangan" id="modalKeterangan"
                                    style="width:100%; padding:0.75rem; border:2px solid #bdd1d3; border-radius:0.6rem;">
                            </div>
                            <div style="display:flex; gap:0.75rem;">
                                <button type="submit"
                                    style="flex:1; padding:0.75rem; background:#002f45; color:#d2c296; border:none; border-radius:0.6rem; font-weight:700; cursor:pointer;">Simpan
                                    Perubahan</button>
                                <button type="button" onclick="closeEditModal()"
                                    style="padding:0.75rem; background:#f9f8f6; border:2px solid #bdd1d3; border-radius:0.6rem; cursor:pointer;">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function openEditModal(id, kehadiran, keterangan) {
                        const modal = document.getElementById('editModal');
                        const form = document.getElementById('editForm');

                        // Set URL action secara dinamis (sesuaikan dengan nama route kamu)
                        form.action = `/panitia/mentoring/detail/${id}`;

                        document.getElementById('modalKehadiran').value = kehadiran;
                        document.getElementById('modalKeterangan').value = keterangan === '—' ? '' : keterangan;

                        modal.style.display = 'flex';
                    }

                    function closeEditModal() {
                        document.getElementById('editModal').style.display = 'none';
                    }
                </script>

                {{-- KONDISI 2: JIKA BELUM PILIH KELOMPOK --}}
            @else
                <div
                    style="background:#002f45; border-radius:1.5rem; padding:3rem; text-align:center; margin-bottom:2rem; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-2rem; right:-2rem; font-size:10rem; opacity:0.05;">👥</div>
                    <h2 style="font-family:'Playfair Display',serif; color:#d2c296; font-size:2rem; margin-bottom:0.5rem;">
                        Pilih Kelompok Mentoring</h2>
                    <p style="color:#bdd1d3; opacity:0.7; font-size:1rem;">Silakan pilih kelompok untuk melakukan absensi
                    </p>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(250px, 1fr)); gap:1.5rem;">
                    @forelse($listKelompok as $k)
                        <a href="{{ route('panitia.mentoring.kelompok', $k) }}"
                            style="text-decoration:none; transition:transform 0.2s;"
                            onmouseover="this.style.transform='translateY(-5px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div
                                style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3; text-align:center;">
                                <div
                                    style="color:#002f45; opacity:0.4; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">
                                    Kelompok</div>
                                <div
                                    style="font-family:'Playfair Display',serif; color:#002f45; font-size:3rem; font-weight:800;">
                                    {{ $k }}</div>
                                <div
                                    style="margin-top:1rem; display:inline-block; padding:0.5rem 1.5rem; background:#e0decd; color:#002f45; border-radius:0.6rem; font-size:0.875rem; font-weight:700;">
                                    Buka Presensi →
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            style="grid-column:1/-1; background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px dashed #bdd1d3;">
                            <p style="color:#002f45; opacity:0.5;">Belum ada data kelompok peserta terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            @endif

        </div>
    </div>
@endsection

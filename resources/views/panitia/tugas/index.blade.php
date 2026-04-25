@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:900px; margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('panitia.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">← Kembali ke Portal Panitia</a>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">📚 Kelola Tugas</h1>
            <p style="color:#002f45; opacity:0.5; font-size:0.875rem;">Buat dan kelola jenis tugas yang harus dikumpulkan peserta</p>
        </div>
        <div style="display:flex; gap:0.75rem;">
            <a href="{{ route('panitia.tugas.rekap') }}"
               style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:600; border:2px solid #bdd1d3;">
                📊 Lihat Rekap
            </a>
            <button onclick="toggleForm()"
               style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; border:none; cursor:pointer; font-size:0.875rem; font-weight:700;">
                + Buat Tugas Baru
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="padding:0.875rem 1rem; background:#dcfce7; border:1px solid #86efac; border-radius:0.75rem; color:#166534; margin-bottom:1.5rem; font-size:0.875rem;">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if ($errors->any())
    <div style="padding:0.875rem 1rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:0.75rem; color:#991b1b; margin-bottom:1.5rem; font-size:0.875rem;">
        @foreach ($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
    @endif

    {{-- Form Buat Tugas --}}
    <div id="form-tugas" style="display:none; margin-bottom:2rem;">
    <div style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3;">
        <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
            ✏️ Buat Jenis Tugas Baru
            <button onclick="toggleForm()" style="margin-left:auto; background:none; border:none; color:#002f45; opacity:0.4; cursor:pointer; font-size:1.25rem;">×</button>
        </h3>

        <form method="POST" action="{{ route('panitia.tugas.store') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                <div style="grid-column:1/-1;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Nama Tugas *</label>
                    <input type="text" name="nama_tugas" value="{{ old('nama_tugas') }}" required
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; box-sizing:border-box; outline:none;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="Contoh: Tugas 1 — Essay Motivasi, Puisi Kebangsaan, dst.">
                </div>

                <div style="grid-column:1/-1;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Deskripsi / Petunjuk <span style="font-weight:400; opacity:0.5;">(opsional)</span></label>
                    <textarea name="deskripsi" rows="2"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="Jelaskan tugas ini: ketentuan, format, dll.">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Deadline <span style="font-weight:400; opacity:0.5;">(opsional)</span></label>
                    <input type="datetime-local" name="deadline" value="{{ old('deadline') }}"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; box-sizing:border-box; outline:none;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'">
                    <p style="color:#002f45; opacity:0.4; font-size:0.72rem; margin-top:0.3rem;">Kosongkan jika tidak ada batas waktu.</p>
                </div>

                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Tipe File yang Diizinkan *</label>
                    <select name="tipe_file"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; background:white; outline:none; box-sizing:border-box;">
                        <option value="semua"  {{ old('tipe_file') === 'semua'  ? 'selected' : '' }}>Semua (PDF, gambar, doc, zip)</option>
                        <option value="pdf"    {{ old('tipe_file') === 'pdf'    ? 'selected' : '' }}>PDF saja</option>
                        <option value="gambar" {{ old('tipe_file') === 'gambar' ? 'selected' : '' }}>Gambar saja (jpg/png)</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Maks. Ukuran File *</label>
                    <select name="maks_ukuran"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; background:white; outline:none; box-sizing:border-box;">
                        <option value="2048"  {{ old('maks_ukuran') == 2048  ? 'selected' : '' }}>2 MB</option>
                        <option value="5120"  {{ old('maks_ukuran') == 5120  ? 'selected' : '' }} selected>5 MB (default)</option>
                        <option value="10240" {{ old('maks_ukuran') == 10240 ? 'selected' : '' }}>10 MB</option>
                        <option value="20480" {{ old('maks_ukuran') == 20480 ? 'selected' : '' }}>20 MB</option>
                    </select>
                </div>

                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Urutan Tampil</label>
                    <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; box-sizing:border-box; outline:none;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="0, 1, 2 …">
                    <p style="color:#002f45; opacity:0.4; font-size:0.72rem; margin-top:0.3rem;">Angka kecil tampil lebih dulu.</p>
                </div>
            </div>

            <div style="display:flex; gap:0.75rem;">
                <button type="submit"
                    style="flex:1; padding:0.875rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.95rem;"
                    onmouseover="this.style.background='#00405e'" onmouseout="this.style.background='#002f45'">
                    🗂 Buat Tugas
                </button>
                <button type="button" onclick="toggleForm()"
                    style="padding:0.875rem 1.5rem; background:transparent; color:#002f45; border:2px solid #bdd1d3; border-radius:0.75rem; cursor:pointer; font-size:0.875rem; font-weight:600;">
                    Batal
                </button>
            </div>
        </form>
    </div>
    </div>

    {{-- Daftar Tugas --}}
    @if($tugasList->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <div style="font-size:3rem; margin-bottom:1rem;">📚</div>
        <p style="color:#002f45; opacity:0.5; margin-bottom:1rem;">Belum ada jenis tugas yang dibuat.</p>
        <button onclick="toggleForm()"
            style="padding:0.75rem 2rem; background:#002f45; color:#d2c296; border:none; border-radius:0.75rem; cursor:pointer; font-weight:700; font-size:0.875rem;">
            + Buat Tugas Pertama
        </button>
    </div>
    @else
    <div style="display:flex; flex-direction:column; gap:1rem;">
        @foreach($tugasList as $tugas)
        <div style="background:white; border-radius:1rem; padding:1.25rem 1.5rem; border:2px solid {{ $tugas->aktif ? '#bdd1d3' : '#e0decd' }}; opacity:{{ $tugas->aktif ? '1' : '0.65' }};">
            <div style="display:flex; align-items:flex-start; gap:1rem; flex-wrap:wrap;">
                {{-- Info utama --}}
                <div style="flex:1; min-width:220px;">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.35rem; flex-wrap:wrap;">
                        <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin:0;">{{ $tugas->nama_tugas }}</h3>
                        <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700;
                            {{ $tugas->aktif ? 'background:#dcfce7; color:#166534;' : 'background:#f3f4f6; color:#6b7280;' }}">
                            {{ $tugas->aktif ? '● Aktif' : '● Nonaktif' }}
                        </span>
                        @if($tugas->isTerlambat())
                        <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700; background:#fee2e2; color:#991b1b;">
                            ⏰ Deadline Lewat
                        </span>
                        @endif
                    </div>
                    @if($tugas->deskripsi)
                    <p style="color:#002f45; opacity:0.6; font-size:0.8rem; margin:0 0 0.5rem 0; line-height:1.5;">{{ $tugas->deskripsi }}</p>
                    @endif
                    <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                        @if($tugas->deadline)
                        <span style="color:#d97706; font-size:0.75rem;">⏰ {{ $tugas->deadline->format('d M Y, H:i') }}</span>
                        @endif
                        <span style="color:#002f45; opacity:0.5; font-size:0.75rem;">
                            📎 {{ strtoupper($tugas->tipe_file) }} · maks {{ round($tugas->maks_ukuran/1024,0) }} MB
                        </span>
                        <span style="color:#002f45; opacity:0.5; font-size:0.75rem;">
                            #{{ $tugas->urutan }} · dibuat {{ $tugas->created_at->format('d/m') }}
                        </span>
                    </div>
                </div>

                {{-- Progress pengumpulan --}}
                <div style="text-align:center; padding:0 1rem; border-left:1px solid #e0decd;">
                    <div style="color:#002f45; font-size:1.5rem; font-weight:800; font-family:'Playfair Display',serif;">
                        {{ $tugas->pengumpulan_count }}
                    </div>
                    <div style="color:#002f45; opacity:0.5; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">Dikumpulkan</div>
                    <div style="color:#002f45; opacity:0.4; font-size:0.7rem;">dari {{ $totalPeserta }} peserta</div>
                    @if($totalPeserta > 0)
                    <div style="margin-top:0.4rem; background:#e0decd; border-radius:999px; height:4px; width:80px;">
                        <div style="background:#002f45; height:4px; border-radius:999px; width:{{ min(100, round(($tugas->pengumpulan_count/$totalPeserta)*100)) }}%;"></div>
                    </div>
                    @endif
                </div>

                {{-- Aksi --}}
                <div style="display:flex; flex-direction:column; gap:0.5rem; min-width:120px;">
                    <form method="POST" action="{{ route('panitia.tugas.toggle', $tugas->id) }}" style="margin:0;">
                        @csrf
                        <button type="submit"
                            style="width:100%; padding:0.4rem 0.75rem; background:{{ $tugas->aktif ? '#fef9c3' : '#dcfce7' }}; color:{{ $tugas->aktif ? '#854d0e' : '#166534' }}; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                            {{ $tugas->aktif ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('panitia.tugas.destroy', $tugas->id) }}" style="margin:0;"
                        onsubmit="return confirm('Hapus tugas ini beserta SEMUA file pengumpulan peserta?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            style="width:100%; padding:0.4rem 0.75rem; background:#fee2e2; color:#991b1b; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                            🗑 Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
</div>

<script>
function toggleForm() {
    const f = document.getElementById('form-tugas');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
    if (f.style.display === 'block') f.scrollIntoView({ behavior:'smooth', block:'start' });
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('form-tugas').style.display = 'block';
});
@endif
</script>
@endsection

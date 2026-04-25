@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:1200px; margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <a href="{{ route('panitia.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:0.25rem;">← Kembali ke Portal Panitia</a>
            <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700;">📒 Kas Event</h1>
            <p style="color:#002f45; opacity:0.5; font-size:0.875rem;">Pencatatan pemasukan dan pengeluaran kas panitia</p>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center;">
            <a href="{{ route('panitia.kas.export', request()->only('jenis','divisi')) }}"
               style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.6rem; text-decoration:none; font-size:0.875rem; font-weight:600; border:2px solid #bdd1d3;">
                ⬇ Export Excel
            </a>
            <button onclick="toggleForm()"
               style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border-radius:0.6rem; border:none; cursor:pointer; font-size:0.875rem; font-weight:700;">
                + Tambah Transaksi
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
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

    {{-- Summary Cards --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem;">
        <div style="background:#002f45; border-radius:1.25rem; padding:1.5rem; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;">💰</div>
            <div style="color:#bdd1d3; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Saldo Saat Ini</div>
            <div style="color:{{ $saldoAkhir >= 0 ? '#d2c296' : '#fca5a5' }}; font-size:1.75rem; font-weight:800; font-family:'Playfair Display',serif;">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </div>
            <div style="color:#bdd1d3; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">Total saldo berjalan</div>
        </div>
        <div style="background:white; border-radius:1.25rem; padding:1.5rem; border:2px solid #86efac; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;">⬆</div>
            <div style="color:#166534; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Total Pemasukan</div>
            <div style="color:#166534; font-size:1.5rem; font-weight:800;">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <div style="color:#166534; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">
                {{ \App\Models\KasTransaksi::where('jenis','masuk')->count() }} transaksi masuk
            </div>
        </div>
        <div style="background:white; border-radius:1.25rem; padding:1.5rem; border:2px solid #fca5a5; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;">⬇</div>
            <div style="color:#991b1b; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Total Pengeluaran</div>
            <div style="color:#991b1b; font-size:1.5rem; font-weight:800;">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <div style="color:#991b1b; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">
                {{ \App\Models\KasTransaksi::where('jenis','keluar')->count() }} transaksi keluar
            </div>
        </div>
    </div>

    {{-- Form Tambah Transaksi (hidden by default) --}}
    <div id="form-transaksi" style="display:none; margin-bottom:2rem;">
        <div style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3;">
            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                📝 Form Input Transaksi
                <button onclick="toggleForm()" style="margin-left:auto; background:none; border:none; color:#002f45; opacity:0.4; cursor:pointer; font-size:1.25rem; line-height:1;">×</button>
            </h3>

            <form method="POST" action="{{ route('panitia.kas.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    {{-- Tanggal --}}
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;"
                            onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'">
                    </div>

                    {{-- Jenis --}}
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Jenis *</label>
                        <select name="jenis" id="jenis-select" required onchange="handleJenisChange(this.value)"
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; background:white; outline:none; box-sizing:border-box;">
                            <option value="">-- Pilih --</option>
                            <option value="masuk"  {{ old('jenis') === 'masuk'  ? 'selected' : '' }}>⬆ Uang Masuk</option>
                            <option value="keluar" {{ old('jenis') === 'keluar' ? 'selected' : '' }}>⬇ Uang Keluar</option>
                        </select>
                    </div>

                    {{-- Nominal --}}
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Nominal (Rp) *</label>
                        <input type="number" name="nominal" value="{{ old('nominal') }}" min="1" required
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;"
                            onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                            placeholder="Contoh: 500000">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    {{-- Divisi (hanya muncul saat keluar) --}}
                    <div id="divisi-section" style="display:none;">
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Divisi *</label>
                        <select name="divisi" id="divisi-select"
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; background:white; outline:none; box-sizing:border-box;">
                            <option value="">-- Pilih Divisi --</option>
                            @foreach($divisiList as $d)
                            <option value="{{ $d }}" {{ old('divisi') === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PIC --}}
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">PIC (Penanggung Jawab) *</label>
                        <input type="text" name="pic" value="{{ old('pic') }}" required
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;"
                            onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                            placeholder="Nama penanggung jawab">
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Keterangan *</label>
                    <textarea name="keterangan" required rows="2"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="Deskripsi singkat transaksi">{{ old('keterangan') }}</textarea>
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">
                        Bukti Transaksi <span style="font-weight:400; opacity:0.5;">(opsional, maks 5MB: jpg/png/pdf)</span>
                    </label>
                    <label for="bukti-upload"
                        style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; border:2px dashed #bdd1d3; border-radius:0.6rem; cursor:pointer; background:#f9f8f6;"
                        onmouseover="this.style.borderColor='#002f45'" onmouseout="this.style.borderColor='#bdd1d3'">
                        <span style="font-size:1.5rem;">📎</span>
                        <span style="color:#002f45; font-size:0.8rem;" id="bukti-label">Klik untuk upload bukti (nota, kwitansi, dll)</span>
                        <input type="file" id="bukti-upload" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;"
                            onchange="document.getElementById('bukti-label').textContent = this.files[0]?.name || 'Klik untuk upload'">
                    </label>
                </div>

                <div style="display:flex; gap:0.75rem;">
                    <button type="submit"
                        style="flex:1; padding:0.875rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.95rem;"
                        onmouseover="this.style.background='#00405e'" onmouseout="this.style.background='#002f45'">
                        💾 Simpan Transaksi
                    </button>
                    <button type="button" onclick="toggleForm()"
                        style="padding:0.875rem 1.5rem; background:transparent; color:#002f45; font-weight:600; border:2px solid #bdd1d3; border-radius:0.75rem; cursor:pointer; font-size:0.875rem;">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan per Divisi --}}
    @if($ringkasanDivisi->isNotEmpty())
    <div style="background:white; border-radius:1rem; padding:1.25rem; border:2px solid #bdd1d3; margin-bottom:1.5rem;">
        <div style="font-size:0.75rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">Pengeluaran per Divisi</div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            @foreach($ringkasanDivisi as $r)
            <div style="background:#e0decd; border-radius:0.6rem; padding:0.5rem 0.875rem; text-align:center;">
                <div style="color:#002f45; font-size:0.7rem; font-weight:700; text-transform:uppercase;">{{ $r->divisi }}</div>
                <div style="color:#002f45; font-size:0.8rem; font-weight:600;">Rp {{ number_format($r->total, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filter --}}
    <div style="background:white; border-radius:1rem; padding:1rem 1.25rem; border:2px solid #bdd1d3; margin-bottom:1rem;">
        <form method="GET" action="{{ route('panitia.kas.index') }}" style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:600; color:#002f45; margin-bottom:0.3rem; text-transform:uppercase; letter-spacing:0.05em;">Jenis</label>
                <select name="jenis"
                    style="padding:0.6rem 1rem; border:2px solid #bdd1d3; border-radius:0.5rem; font-size:0.875rem; color:#002f45; background:white; outline:none;">
                    <option value="">Semua</option>
                    <option value="masuk"  {{ request('jenis') === 'masuk'  ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ request('jenis') === 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:600; color:#002f45; margin-bottom:0.3rem; text-transform:uppercase; letter-spacing:0.05em;">Divisi</label>
                <select name="divisi"
                    style="padding:0.6rem 1rem; border:2px solid #bdd1d3; border-radius:0.5rem; font-size:0.875rem; color:#002f45; background:white; outline:none;">
                    <option value="">Semua Divisi</option>
                    @foreach($divisiList as $d)
                    <option value="{{ $d }}" {{ request('divisi') === $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                style="padding:0.6rem 1.25rem; background:#002f45; color:#d2c296; border:none; border-radius:0.5rem; cursor:pointer; font-weight:600; font-size:0.875rem;">
                Filter
            </button>
            @if(request('jenis') || request('divisi'))
            <a href="{{ route('panitia.kas.index') }}"
               style="padding:0.6rem 1.25rem; background:#e0decd; color:#002f45; border-radius:0.5rem; text-decoration:none; font-weight:600; font-size:0.875rem;">
                Reset
            </a>
            @endif
            <span style="margin-left:auto; color:#002f45; opacity:0.5; font-size:0.8rem; align-self:center;">
                {{ $transaksi->count() }} transaksi ditampilkan
            </span>
        </form>
    </div>

    {{-- Tabel Transaksi --}}
    @if($transaksi->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <div style="font-size:3rem; margin-bottom:1rem;">📒</div>
        <p style="color:#002f45; opacity:0.5;">Belum ada transaksi yang dicatat.</p>
        <button onclick="toggleForm()" style="margin-top:1rem; padding:0.75rem 2rem; background:#002f45; color:#d2c296; border:none; border-radius:0.75rem; cursor:pointer; font-weight:700;">
            + Tambah Transaksi Pertama
        </button>
    </div>
    @else
    <div style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:900px;">
                <thead>
                    <tr style="background:#002f45;">
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">No</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">Tanggal</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Jenis</th>
                        <th style="padding:0.75rem 1rem; text-align:right; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">Nominal (Rp)</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Divisi</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Keterangan</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">PIC</th>
                        <th style="padding:0.75rem 1rem; text-align:right; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;">Saldo (Rp)</th>
                        <th style="padding:0.75rem 1rem; text-align:center; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Bukti</th>
                        <th style="padding:0.75rem 1rem; text-align:center; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi as $i => $t)
                    <tr style="border-bottom:1px solid #e0decd; {{ $t->jenis === 'masuk' ? 'background:#f0fdf4;' : ($loop->even ? '#fff5f5' : 'background:#fff8f8;') }}">
                        <td style="padding:0.75rem 1rem; color:#002f45; opacity:0.4; font-size:0.8rem;">{{ $i + 1 }}</td>
                        <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.8rem; white-space:nowrap;">{{ $t->tanggal->format('d/m/Y') }}</td>
                        <td style="padding:0.75rem 1rem;">
                            <span style="display:inline-block; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.7rem; font-weight:700;
                                {{ $t->jenis === 'masuk' ? 'background:#dcfce7; color:#166534;' : 'background:#fee2e2; color:#991b1b;' }}">
                                {{ $t->jenis === 'masuk' ? '⬆ MASUK' : '⬇ KELUAR' }}
                            </span>
                        </td>
                        <td style="padding:0.75rem 1rem; color:{{ $t->jenis === 'masuk' ? '#166534' : '#991b1b' }}; font-weight:700; font-size:0.875rem; text-align:right; white-space:nowrap;">
                            {{ $t->jenis === 'masuk' ? '+' : '-' }} {{ number_format($t->nominal, 0, ',', '.') }}
                        </td>
                        <td style="padding:0.75rem 1rem; font-size:0.8rem;">
                            @if($t->divisi)
                            <span style="background:#e0decd; color:#002f45; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.7rem; font-weight:600;">{{ $t->divisi }}</span>
                            @else
                            <span style="color:#002f45; opacity:0.3;">—</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.8rem; max-width:200px;">{{ $t->keterangan }}</td>
                        <td style="padding:0.75rem 1rem; color:#002f45; font-size:0.8rem;">{{ $t->pic }}</td>
                        <td style="padding:0.75rem 1rem; text-align:right; white-space:nowrap;">
                            <span style="font-weight:700; font-size:0.875rem; color:{{ $t->saldo_berjalan >= 0 ? '#166534' : '#991b1b' }};">
                                {{ number_format($t->saldo_berjalan, 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="padding:0.75rem 1rem; text-align:center;">
                            @if($t->bukti_file)
                            <a href="{{ Storage::url($t->bukti_file) }}" target="_blank"
                               style="color:#002f45; font-size:1.1rem; text-decoration:none;" title="Lihat bukti">📄</a>
                            @else
                            <span style="color:#002f45; opacity:0.2; font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem; text-align:center;">
                            <form method="POST" action="{{ route('panitia.kas.destroy', $t->id) }}" style="display:inline;"
                                onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    style="padding:0.3rem 0.75rem; background:#fee2e2; color:#991b1b; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#e0decd;">
                        <td colspan="3" style="padding:0.875rem 1rem; color:#002f45; font-weight:700; font-size:0.875rem;">TOTAL</td>
                        <td style="padding:0.875rem 1rem; text-align:right; font-weight:700; font-size:0.875rem; color:#002f45;">
                            {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}
                        </td>
                        <td colspan="3" style="padding:0.875rem 1rem;"></td>
                        <td style="padding:0.875rem 1rem; text-align:right; font-weight:800; font-size:1rem; color:{{ $saldoAkhir >= 0 ? '#166534' : '#991b1b' }};">
                            {{ number_format($saldoAkhir, 0, ',', '.') }}
                        </td>
                        <td colspan="2" style="padding:0.875rem 1rem;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
</div>

<script>
function toggleForm() {
    const form = document.getElementById('form-transaksi');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    if (form.style.display === 'block') {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function handleJenisChange(val) {
    const divisiSection = document.getElementById('divisi-section');
    const divisiSelect  = document.getElementById('divisi-select');

    if (val === 'keluar') {
        divisiSection.style.display = 'block';
        divisiSelect.required = true;
    } else {
        divisiSection.style.display = 'none';
        divisiSelect.required = false;
        divisiSelect.value = '';
    }
}

// Buka form otomatis kalau ada error validasi
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('form-transaksi').style.display = 'block';
    handleJenisChange('{{ old('jenis', '') }}');
});
@endif
</script>
@endsection

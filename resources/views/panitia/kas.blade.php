@extends('layouts.app')

@section('content')
{{-- Tambahan CSS khusus responsif tanpa merubah inline style di bawah --}}
<style>
    @media (max-width: 768px) {
        /* Membuat Summary Cards tumpuk vertikal di HP */
        .summary-grid-mobile { 
            grid-template-columns: 1fr !important; 
        }
        /* Membuat Form Input tumpuk vertikal di HP */
        .form-grid-mobile { 
            grid-template-columns: 1fr !important; 
        }
        /* Header biar tidak tabrakan di HP */
        .header-mobile {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
    }
</style>

<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:1200px; margin:0 auto;">

    {{-- Header --}}
    <div class="header-mobile" style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem;">
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

    {{-- Summary Cards (Ditambah class summary-grid-mobile) --}}
    <div class="summary-grid-mobile" style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:2rem;">
        <div style="background:#002f45; border-radius:1.25rem; padding:1.5rem; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;"></div>
            <div style="color:#bdd1d3; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Saldo Saat Ini</div>
            <div style="color:{{ $saldoAkhir >= 0 ? '#d2c296' : '#fca5a5' }}; font-size:1.75rem; font-weight:800; font-family:'Playfair Display',serif;">
                Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
            </div>
            <div style="color:#bdd1d3; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">Total saldo berjalan</div>
        </div>
        <div style="background:white; border-radius:1.25rem; padding:1.5rem; border:2px solid #86efac; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;"></div>
            <div style="color:#166534; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Total Pemasukan</div>
            <div style="color:#166534; font-size:1.5rem; font-weight:800;">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </div>
            <div style="color:#166534; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">
                {{ \App\Models\KasTransaksi::where('jenis','masuk')->count() }} transaksi masuk
            </div>
        </div>
        <div style="background:white; border-radius:1.25rem; padding:1.5rem; border:2px solid #fca5a5; position:relative; overflow:hidden;">
            <div style="position:absolute; top:-1rem; right:-1rem; font-size:5rem; opacity:0.07;"></div>
            <div style="color:#991b1b; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:0.5rem;">Total Pengeluaran</div>
            <div style="color:#991b1b; font-size:1.5rem; font-weight:800;">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </div>
            <div style="color:#991b1b; font-size:0.75rem; margin-top:0.5rem; opacity:0.7;">
                {{ \App\Models\KasTransaksi::where('jenis','keluar')->count() }} transaksi keluar
            </div>
        </div>
    </div>

    {{-- Form Tambah Transaksi --}}
    <div id="form-transaksi" style="display:none; margin-bottom:2rem;">
        <div style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3;">
            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem;">
                📝 Form Input Transaksi
                <button onclick="toggleForm()" style="margin-left:auto; background:none; border:none; color:#002f45; opacity:0.4; cursor:pointer; font-size:1.25rem; line-height:1;">×</button>
            </h3>

            <form method="POST" action="{{ route('panitia.kas.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid-mobile" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    {{-- Tanggal --}}
                    <div>
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Tanggal *</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;">
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
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;">
                    </div>
                </div>

                <div class="form-grid-mobile" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    {{-- Divisi --}}
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
                            style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;">
                    </div>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Keterangan *</label>
                    <textarea name="keterangan" required rows="2"
                        style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Bukti Transaksi --}}
                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">Bukti Transaksi</label>
                    <label for="bukti-upload" style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; border:2px dashed #bdd1d3; border-radius:0.6rem; cursor:pointer; background:#f9f8f6;">
                        <span style="font-size:1.5rem;">📎</span>
                        <span style="color:#002f45; font-size:0.8rem;" id="bukti-label">Klik untuk upload bukti</span>
                        <input type="file" id="bukti-upload" name="bukti_file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;"
                            onchange="document.getElementById('bukti-label').textContent = this.files[0]?.name || 'Klik untuk upload'">
                    </label>
                </div>

                <div style="display:flex; gap:0.75rem;">
                    <button type="submit" style="flex:1; padding:0.875rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.95rem;">💾 Simpan Transaksi</button>
                    <button type="button" onclick="toggleForm()" style="padding:0.875rem 1.5rem; background:transparent; color:#002f45; font-weight:600; border:2px solid #bdd1d3; border-radius:0.75rem; cursor:pointer; font-size:0.875rem;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Transaksi (Tetap pakai inline style kamu, cuma ditambah overflow-x agar tabel bisa digeser di HP) --}}
    <div style="background:white; border-radius:1rem; overflow:hidden; border:1px solid #bdd1d3;">
        <div style="overflow-x:auto;"> {{-- Bungkus tabel agar bisa discroll di HP --}}
            <table style="width:100%; border-collapse:collapse; min-width:900px;">
                <thead>
                    <tr style="background:#002f45;">
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; white-space:nowrap;">No</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; white-space:nowrap;">Tanggal</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">Jenis</th>
                        <th style="padding:0.75rem 1rem; text-align:right; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; white-space:nowrap;">Nominal (Rp)</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">Divisi</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">Keterangan</th>
                        <th style="padding:0.75rem 1rem; text-align:left; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">PIC</th>
                        <th style="padding:0.75rem 1rem; text-align:right; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase; white-space:nowrap;">Saldo (Rp)</th>
                        <th style="padding:0.75rem 1rem; text-align:center; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">Bukti</th>
                        <th style="padding:0.75rem 1rem; text-align:center; color:#d2c296; font-size:0.75rem; font-weight:600; text-transform:uppercase;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi as $i => $t)
                    <tr style="border-bottom:1px solid #e0decd; {{ $t->jenis === 'masuk' ? 'background:#f0fdf4;' : ($loop->even ? 'background:#fff5f5' : 'background:#fff8f8;') }}">
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
                            <a href="{{ Storage::url($t->bukti_file) }}" target="_blank" style="color:#002f45; font-size:1.1rem; text-decoration:none;">📄</a>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem; text-align:center;">
                            <form method="POST" action="{{ route('panitia.kas.destroy', $t->id) }}" style="display:inline;" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:0.3rem 0.75rem; background:#fee2e2; color:#991b1b; border:none; border-radius:0.4rem; cursor:pointer; font-size:0.75rem; font-weight:600;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
function toggleForm() {
    const form = document.getElementById('form-transaksi');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function handleJenisChange(val) {
    const div = document.getElementById('divisi-section');
    const sel = document.getElementById('divisi-select');
    if (val === 'keluar') {
        div.style.display = 'block';
        sel.required = true;
    } else {
        div.style.display = 'none';
        sel.required = false;
        sel.value = '';
    }
}
</script>
@endsection
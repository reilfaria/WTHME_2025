@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:600px; margin:0 auto;">

    <a href="{{ route('peserta.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:1.5rem;">
        ← Kembali
    </a>

    <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700; margin-bottom:0.25rem;">
        Formulir Riwayat Kesehatan
    </h1>
    <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-bottom:2rem;">
        Data ini bersifat rahasia dan hanya digunakan untuk keperluan medis selama PKKMB.
    </p>

    @if(session('success'))
    <div style="padding:1rem; background:#dcfce7; border:1px solid #86efac; border-radius:0.75rem; color:#166534; margin-bottom:1.5rem; font-size:0.875rem;">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div style="padding:1rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:0.75rem; color:#991b1b; margin-bottom:1.5rem; font-size:0.875rem;">
        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <div style="background:white; border-radius:1rem; padding:2rem; border:2px solid #bdd1d3;">

        <form method="POST" action="{{ route('peserta.riwayat.store') }}" style="display:flex; flex-direction:column; gap:1.5rem;">
            @csrf

            {{-- Info otomatis (readonly) --}}
            <div style="background:#e0decd; border-radius:0.75rem; padding:1rem; display:flex; gap:2rem; flex-wrap:wrap;">
                <div>
                    <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Nama</div>
                    <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ auth()->user()->name }}</div>
                </div>
                <div>
                    <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">NIM</div>
                    <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ auth()->user()->nim }}</div>
                </div>
                <div>
                    <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em;">Kelompok</div>
                    <div style="color:#002f45; font-weight:600; font-size:0.875rem;">{{ auth()->user()->kelompok }}</div>
                </div>
            </div>

            {{-- Kondisi Kesehatan Umum --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#002f45; margin-bottom:0.6rem; text-transform:uppercase; letter-spacing:0.05em;">
                    Kondisi Kesehatan Saat Ini *
                </label>
                <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                    @foreach(['Baik', 'Cukup', 'Perlu Perhatian'] as $kondisi)
                    <label style="display:flex; align-items:center; gap:0.5rem; padding:0.6rem 1.25rem; border:2px solid #bdd1d3; border-radius:0.6rem; cursor:pointer;
                                  {{ old('kondisi_kesehatan', $data->kondisi_kesehatan ?? '') === $kondisi ? 'border-color:#002f45; background:#002f45; color:white;' : 'color:#002f45;' }}">
                        <input type="radio" name="kondisi_kesehatan" value="{{ $kondisi }}" style="display:none;"
                               {{ old('kondisi_kesehatan', $data->kondisi_kesehatan ?? '') === $kondisi ? 'checked' : '' }}>
                        {{ $kondisi }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Riwayat Penyakit --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">
                    Riwayat Penyakit
                    <span style="font-weight:400; text-transform:none; letter-spacing:0; opacity:0.5; font-size:0.75rem;"> (kosongkan jika tidak ada)</span>
                </label>
                <textarea name="riwayat_penyakit" rows="3"
                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                    onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                    placeholder="Contoh: Asma, Diabetes, Hipertensi...">{{ old('riwayat_penyakit', $data->riwayat_penyakit ?? '') }}</textarea>
            </div>

            {{-- Alergi --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">
                    Alergi
                    <span style="font-weight:400; text-transform:none; letter-spacing:0; opacity:0.5; font-size:0.75rem;"> (makanan, obat, dll)</span>
                </label>
                <textarea name="alergi" rows="2"
                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                    onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                    placeholder="Contoh: Alergi seafood, Alergi penisilin...">{{ old('alergi', $data->alergi ?? '') }}</textarea>
            </div>

            {{-- Obat Rutin --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">
                    Obat yang Rutin Dikonsumsi
                    <span style="font-weight:400; text-transform:none; letter-spacing:0; opacity:0.5; font-size:0.75rem;"> (jika ada)</span>
                </label>
                <textarea name="obat_rutin" rows="2"
                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                    onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                    placeholder="Contoh: Metformin 500mg, Salbutamol inhaler...">{{ old('obat_rutin', $data->obat_rutin ?? '') }}</textarea>
            </div>

            {{-- Keterangan Tambahan --}}
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:600; color:#002f45; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.05em;">
                    Keterangan Tambahan
                </label>
                <textarea name="keterangan_tambahan" rows="2"
                    style="width:100%; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none; resize:vertical; font-family:inherit;"
                    onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                    placeholder="Informasi kesehatan lain yang perlu panitia ketahui...">{{ old('keterangan_tambahan', $data->keterangan_tambahan ?? '') }}</textarea>
            </div>

            <button type="submit"
                style="padding:0.875rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.95rem;"
                onmouseover="this.style.background='#00405e'" onmouseout="this.style.background='#002f45'">
                {{ $data ? 'Perbarui Data Kesehatan' : 'Simpan Data Kesehatan' }}
            </button>

        </form>
    </div>

</div>
</div>

{{-- Buat radio button kondisi kesehatan interaktif --}}
<script>
document.querySelectorAll('input[name="kondisi_kesehatan"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="kondisi_kesehatan"]').forEach(r => {
            const label = r.closest('label');
            label.style.borderColor = '#bdd1d3';
            label.style.background  = 'white';
            label.style.color       = '#002f45';
        });
        const selected = this.closest('label');
        selected.style.borderColor = '#002f45';
        selected.style.background  = '#002f45';
        selected.style.color       = 'white';
    });
});
</script>
@endsection
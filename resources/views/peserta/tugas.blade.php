@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:680px; margin:0 auto;">

    <a href="{{ route('peserta.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:1.5rem;">← Kembali ke Portal Peserta</a>

    <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700; margin-bottom:0.25rem;">📚 Pengumpulan Tugas</h1>
    <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-bottom:2rem;">
        Halo, <strong>{{ $user->name }}</strong> — Kelompok {{ $user->kelompok }}
    </p>

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

    {{-- Progress ringkas --}}
    @php $sudahCount = $sudahKumpul->count(); $totalCount = $tugasList->count(); @endphp
    @if($totalCount > 0)
    <div style="background:#002f45; border-radius:1rem; padding:1.25rem 1.5rem; margin-bottom:2rem; display:flex; align-items:center; gap:1.5rem;">
        <div>
            <div style="color:#d2c296; font-size:2rem; font-weight:800; font-family:'Playfair Display',serif; line-height:1;">{{ $sudahCount }}/{{ $totalCount }}</div>
            <div style="color:#bdd1d3; font-size:0.75rem; margin-top:0.2rem;">tugas dikumpulkan</div>
        </div>
        <div style="flex:1;">
            <div style="background:rgba(255,255,255,0.15); border-radius:999px; height:8px;">
                <div style="background:#d2c296; height:8px; border-radius:999px; width:{{ $totalCount > 0 ? round(($sudahCount/$totalCount)*100) : 0 }}%; transition:width 0.5s;"></div>
            </div>
            <div style="color:#bdd1d3; font-size:0.72rem; margin-top:0.4rem;">{{ $totalCount - $sudahCount }} tugas belum dikumpulkan</div>
        </div>
    </div>
    @endif

    @if($tugasList->isEmpty())
    <div style="background:white; border-radius:1rem; padding:3rem; text-align:center; border:2px solid #bdd1d3;">
        <div style="font-size:3rem; margin-bottom:1rem;">📭</div>
        <p style="color:#002f45; opacity:0.5;">Belum ada tugas yang dibuka oleh panitia.</p>
    </div>
    @else

    {{-- Daftar Tugas --}}
    <div style="display:flex; flex-direction:column; gap:1.25rem;">
        @foreach($tugasList as $tugas)
        @php
            $kumpulan    = $sudahKumpul[$tugas->id] ?? null;
            $sudah       = !is_null($kumpulan);
            $isLewat     = $tugas->isTerlambat();
            $ekstensiOke = implode(', ', array_map('strtoupper', $tugas->ekstensiDiizinkan()));
        @endphp
        <div style="background:white; border-radius:1.25rem; border:2px solid {{ $sudah ? '#86efac' : ($isLewat ? '#fca5a5' : '#bdd1d3') }}; overflow:hidden;">

            {{-- Header tugas --}}
            <div style="padding:1.25rem 1.5rem; {{ $sudah ? 'background:#f0fdf4;' : ($isLewat ? 'background:#fff5f5;' : '') }}">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.4rem;">
                            <h3 style="color:#002f45; font-weight:700; font-size:1rem; margin:0;">{{ $tugas->nama_tugas }}</h3>
                            @if($sudah)
                                @if($kumpulan->status === 'tepat_waktu')
                                <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700; background:#dcfce7; color:#166534;">✓ Sudah Dikumpulkan</span>
                                @else
                                <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700; background:#fee2e2; color:#991b1b;">⚠ Terlambat</span>
                                @endif
                            @elseif($isLewat)
                            <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700; background:#fee2e2; color:#991b1b;">⏰ Deadline Lewat</span>
                            @else
                            <span style="padding:0.15rem 0.6rem; border-radius:999px; font-size:0.65rem; font-weight:700; background:#fef9c3; color:#854d0e;">⏳ Belum Dikumpulkan</span>
                            @endif
                        </div>

                        @if($tugas->deskripsi)
                        <p style="color:#002f45; opacity:0.65; font-size:0.82rem; margin:0 0 0.5rem 0; line-height:1.6;">{{ $tugas->deskripsi }}</p>
                        @endif

                        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                            @if($tugas->deadline)
                            <span style="font-size:0.75rem; color:{{ $isLewat ? '#991b1b' : '#d97706' }}; font-weight:600;">
                                ⏰ Deadline: {{ $tugas->deadline->format('d M Y, H:i') }}
                            </span>
                            @endif
                            <span style="font-size:0.75rem; color:#002f45; opacity:0.5;">
                                📎 {{ $ekstensiOke }} · maks {{ round($tugas->maks_ukuran/1024,0) }} MB
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info file yang sudah dikumpulkan --}}
            @if($sudah)
            <div style="padding:0.875rem 1.5rem; background:#e8f5e9; border-top:1px solid #86efac; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <span style="font-size:1.5rem;">{{ in_array($kumpulan->file_ekstensi, ['jpg','jpeg','png','webp']) ? '🖼' : '📄' }}</span>
                    <div>
                        <div style="color:#166534; font-size:0.8rem; font-weight:600;">{{ $kumpulan->file_nama_asli }}</div>
                        <div style="color:#166534; opacity:0.7; font-size:0.72rem;">
                            {{ $kumpulan->ukuranFormatted() }} · dikumpulkan {{ $kumpulan->dikumpulkan_at->format('d M Y, H:i') }}
                        </div>
                        @if($kumpulan->catatan)
                        <div style="color:#166534; opacity:0.7; font-size:0.72rem; font-style:italic;">Catatan: {{ $kumpulan->catatan }}</div>
                        @endif
                    </div>
                </div>
                <button onclick="toggleUpload({{ $tugas->id }})"
                    style="padding:0.4rem 0.875rem; background:white; color:#166534; border:1px solid #86efac; border-radius:0.5rem; cursor:pointer; font-size:0.75rem; font-weight:600;">
                    🔄 Ganti File
                </button>
            </div>
            @endif

            {{-- Form Upload --}}
            @php $showForm = !$sudah || $errors->has('file_tugas'); @endphp
            <div id="upload-{{ $tugas->id }}" style="display:{{ $showForm ? 'block' : 'none' }}; padding:1.25rem 1.5rem; border-top:1px solid #e0decd;">
                <form method="POST" action="{{ route('peserta.tugas.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tugas_kategori_id" value="{{ $tugas->id }}">

                    {{-- Dropzone file --}}
                    <div style="margin-bottom:1rem;">
                        <label for="file-{{ $tugas->id }}"
                            style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1.5rem; border:2px dashed #bdd1d3; border-radius:0.75rem; cursor:pointer; text-align:center; transition:border-color 0.15s;"
                            onmouseover="this.style.borderColor='#002f45'" onmouseout="this.style.borderColor='#bdd1d3'"
                            id="label-{{ $tugas->id }}">
                            <span style="font-size:2rem; margin-bottom:0.5rem;">📤</span>
                            <span style="color:#002f45; font-weight:600; font-size:0.875rem;" id="file-label-{{ $tugas->id }}">
                                {{ $sudah ? 'Pilih file pengganti' : 'Klik untuk pilih file' }}
                            </span>
                            <span style="color:#002f45; opacity:0.4; font-size:0.75rem; margin-top:0.2rem;">{{ $ekstensiOke }} · maks {{ round($tugas->maks_ukuran/1024,0) }} MB</span>
                            <input type="file" id="file-{{ $tugas->id }}" name="file_tugas"
                                accept="{{ implode(',', array_map(fn($e) => '.'.$e, $tugas->ekstensiDiizinkan())) }}"
                                style="display:none;" required
                                onchange="
                                    const label = document.getElementById('file-label-{{ $tugas->id }}');
                                    const wrap  = document.getElementById('label-{{ $tugas->id }}');
                                    label.textContent = this.files[0]?.name || 'Pilih file';
                                    wrap.style.borderColor = '#002f45';
                                    wrap.style.background  = '#f0f9ff';
                                ">
                        </label>
                    </div>

                    {{-- Catatan opsional --}}
                    <div style="margin-bottom:1rem;">
                        <label style="display:block; font-size:0.75rem; font-weight:600; color:#002f45; margin-bottom:0.3rem; text-transform:uppercase; letter-spacing:0.05em;">
                            Catatan <span style="font-weight:400; opacity:0.5; text-transform:none;">(opsional)</span>
                        </label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}"
                            style="width:100%; padding:0.6rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.875rem; color:#002f45; box-sizing:border-box; outline:none;"
                            onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                            placeholder="Tuliskan catatan untuk panitia (jika ada)">
                    </div>

                    @if($isLewat && !$sudah)
                    <div style="padding:0.6rem 0.875rem; background:#fef9c3; border-radius:0.5rem; color:#854d0e; font-size:0.775rem; margin-bottom:0.875rem;">
                        ⚠ Deadline sudah lewat. File tetap bisa dikumpulkan, namun akan ditandai <strong>Terlambat</strong>.
                    </div>
                    @endif

                    <div style="display:flex; gap:0.75rem;">
                        <button type="submit"
                            style="flex:1; padding:0.75rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:0.9rem;"
                            onmouseover="this.style.background='#00405e'" onmouseout="this.style.background='#002f45'">
                            {{ $sudah ? '🔄 Perbarui File' : '📤 Kumpulkan Tugas' }}
                        </button>
                        @if($sudah)
                        <button type="button" onclick="toggleUpload({{ $tugas->id }})"
                            style="padding:0.75rem 1.25rem; background:transparent; color:#002f45; border:2px solid #bdd1d3; border-radius:0.75rem; cursor:pointer; font-size:0.875rem; font-weight:600;">
                            Batal
                        </button>
                        @endif
                    </div>
                </form>
            </div>

        </div>
        @endforeach
    </div>

    @endif

</div>
</div>

<script>
function toggleUpload(id) {
    const el = document.getElementById('upload-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection

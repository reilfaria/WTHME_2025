@extends('layouts.app')
@if($qrSession->rotating && $qrSession->aktif)
<script>
const sessionCode  = "{{ $qrSession->session_code }}";
const interval     = {{ $qrSession->rotate_interval }} * 1000;
const refreshUrl   = "{{ route('panitia.qr.refresh', $qrSession->session_code) }}";

function hitungSisa(expiresAt) {
    const sisa = Math.max(0, expiresAt - Math.floor(Date.now() / 1000));
    return sisa;
}

async function refreshQr() {
    try {
        const res  = await fetch(refreshUrl);
        const data = await res.json();

        // Update gambar QR
        document.getElementById('qr-container').innerHTML = data.qr_svg;

        // Update countdown
        startCountdown(data.expires_at);

    } catch(e) {
        console.error('Gagal refresh QR:', e);
    }
}

function startCountdown(expiresAt) {
    clearInterval(window._countdownTimer);
    window._countdownTimer = setInterval(() => {
        const sisa = hitungSisa(expiresAt);
        const el   = document.getElementById('countdown');
        if (el) el.textContent = sisa + 's';

        // Bar progress
        const bar = document.getElementById('progress-bar');
        if (bar) bar.style.width = ((sisa / {{ $qrSession->rotate_interval }}) * 100) + '%';

        if (sisa <= 0) {
            clearInterval(window._countdownTimer);
            refreshQr();
        }
    }, 1000);
}

// Mulai refresh otomatis
const expiresAt = {{ $qrSession->token_expires_at ? $qrSession->token_expires_at->timestamp : 'Math.floor(Date.now()/1000) + ' . $qrSession->rotate_interval }};
startCountdown(expiresAt);
</script>

{{-- Tambahkan juga elemen countdown di dalam card QR --}}
<style>
#qr-container svg { width: 280px; height: 280px; }
</style>
@endif

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:520px; margin:0 auto; text-align:center;">

    <a href="{{ route('panitia.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; text-align:left; margin-bottom:1.5rem;">
        ← Kembali ke Portal Panitia
    </a>

    @if(session('success'))
    <div style="padding:0.875rem 1rem; background:#dcfce7; border:1px solid #86efac; border-radius:0.75rem; color:#166534; margin-bottom:1.5rem; font-size:0.875rem; text-align:left;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Card QR --}}
    <div style="background:white; border-radius:1.25rem; padding:2rem; border:2px solid #bdd1d3; margin-bottom:1.5rem;">

        <div style="display:inline-block; padding:0.35rem 1rem; border-radius:999px; margin-bottom:1rem; font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;
             {{ $qrSession->aktif ? 'background:#dcfce7; color:#166534;' : 'background:#fee2e2; color:#991b1b;' }}">
            {{ $qrSession->aktif ? '● Aktif' : '● Nonaktif' }}
        </div>

        <h2 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.5rem; font-weight:700; margin-bottom:0.35rem;">
            {{ $qrSession->nama_sesi }}
        </h2>
        <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-bottom:1.5rem;">
            Untuk: <strong>{{ ucfirst($qrSession->untuk) }}</strong>
            &nbsp;·&nbsp;
            Kode: <strong style="font-family:monospace; font-size:0.9rem;">{{ $qrSession->session_code }}</strong>
        </p>

        @if($qrSession->berlaku_hingga)
        <p style="color:#d97706; font-size:0.8rem; margin-bottom:1.5rem;">
            ⏰ Berlaku hingga: {{ $qrSession->berlaku_hingga->format('d M Y, H:i') }}
        </p>
        @endif

      
        {{-- Ganti bagian QR Code SVG yang lama dengan ini: --}}
        <div id="qr-container" style="display:inline-block; padding:1.25rem; background:white; border:2px solid #e0decd; border-radius:1rem; margin-bottom:1rem;">
            {!! $qrCode !!}
        </div>

        @if($qrSession->rotating)
        <div style="margin-bottom:1.5rem;">
            <div style="background:#e0decd; border-radius:999px; height:6px; overflow:hidden; margin-bottom:0.5rem;">
                <div id="progress-bar" style="height:100%; background:#002f45; width:100%; transition:width 1s linear;"></div>
            </div>
            <p style="color:#002f45; opacity:0.5; font-size:0.8rem;">
                QR berubah dalam <strong id="countdown" style="color:#002f45; opacity:1;">{{ $qrSession->rotate_interval }}s</strong>
            </p>
        </div>
        @endif

        {{-- URL manual --}}
        <div style="background:#e0decd; border-radius:0.6rem; padding:0.75rem 1rem; margin-bottom:1.5rem; text-align:left;">
            <div style="font-size:0.7rem; color:#002f45; opacity:0.5; margin-bottom:0.25rem; text-transform:uppercase; letter-spacing:0.05em;">Link Absensi</div>
            <div style="font-size:0.8rem; color:#002f45; word-break:break-all; font-family:monospace;">{{ $absenUrl }}</div>
        </div>

        {{-- Tombol toggle aktif/nonaktif --}}
        <form method="POST" action="{{ route('panitia.qr.toggle', $qrSession->id) }}" style="display:inline;">
            @csrf
            @method('PATCH')
            <button type="submit"
                style="padding:0.75rem 2rem; border:2px solid {{ $qrSession->aktif ? '#991b1b' : '#16a34a' }};
                       background:transparent; color:{{ $qrSession->aktif ? '#991b1b' : '#16a34a' }};
                       border-radius:0.75rem; cursor:pointer; font-weight:700; font-size:0.875rem;"
                onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                {{ $qrSession->aktif ? '⏹ Tutup Sesi Absensi' : '▶ Buka Kembali Sesi' }}
            </button>
        </form>
    </div>

    {{-- Petunjuk penggunaan --}}
    <div style="background:#002f45; border-radius:1rem; padding:1.5rem; text-align:left;">
        <h3 style="color:#d2c296; font-weight:700; font-size:0.9rem; margin-bottom:1rem;">📋 Cara Menggunakan QR Ini</h3>
        <div style="color:#bdd1d3; font-size:0.8rem; line-height:1.8;">
            <p>1. Tampilkan QR Code ini di layar / proyektor</p>
            <p>2. Pastikan {{ $qrSession->untuk === 'peserta' ? 'peserta' : 'panitia' }} terhubung ke WiFi Untirta</p>
            <p>3. {{ ucfirst($qrSession->untuk) }} scan QR pakai kamera HP atau buka Portal {{ ucfirst($qrSession->untuk) }}</p>
            <p>4. Setelah sesi selesai, klik <strong style="color:#d2c296;">"Tutup Sesi Absensi"</strong></p>
        </div>
    </div>

</div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div style="min-height:calc(100vh - 64px); padding:2rem 1.5rem;">
<div style="max-width:480px; margin:0 auto;">

    <a href="{{ route('peserta.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:1.5rem;">
        ← Kembali
    </a>

    <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.75rem; font-weight:700; margin-bottom:0.5rem;">
        Absensi Kegiatan
    </h1>
    <p style="color:#002f45; opacity:0.5; font-size:0.875rem; margin-bottom:2rem;">
        Scan QR Code dari layar/proyektor. Pastikan WiFi Untirta aktif &amp; izinkan akses lokasi.
    </p>

    @if(session('error'))
    <div style="padding:1rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:0.75rem; color:#991b1b; margin-bottom:1.5rem; font-size:0.875rem;">
        {{ session('error') }}
    </div>
    @endif

    @if($error)
    <div style="padding:1rem; background:#fee2e2; border-radius:0.75rem; color:#991b1b; margin-bottom:1.5rem; font-size:0.875rem;">
        {{ $error }}
    </div>
    @endif

    @if(session('success'))
    <div style="padding:2rem; background:#dcfce7; border-radius:1rem; text-align:center; border:2px solid #86efac;">
        <div style="font-size:3rem; margin-bottom:1rem;">✅</div>
        <h3 style="color:#166534; font-weight:700; font-size:1.1rem;">{{ session('success') }}</h3>
    </div>

    @elseif($sudahAbsen)
    <div style="padding:2rem; background:#dcfce7; border-radius:1rem; text-align:center; border:2px solid #86efac;">
        <div style="font-size:3rem; margin-bottom:1rem;">✅</div>
        <h3 style="color:#166534; font-weight:700; font-size:1.1rem;">Sudah Absen!</h3>
        <p style="color:#166534; opacity:0.8; font-size:0.875rem;">Kehadiran kamu sudah tercatat.</p>
    </div>

    @elseif($qrSession && !$error)
    {{-- Status lokasi --}}
    <div id="geo-status" style="padding:0.75rem 1rem; background:#fef9c3; border:1px solid #fde047; border-radius:0.75rem; color:#854d0e; margin-bottom:1.25rem; font-size:0.8rem; display:flex; align-items:center; gap:0.5rem;">
        <span>📍</span> <span id="geo-text">Mengambil lokasi kamu...</span>
    </div>

    <div style="background:white; border-radius:1rem; padding:1.5rem; border:2px solid #bdd1d3; margin-bottom:1.5rem;">
        <div style="font-size:0.7rem; color:#002f45; opacity:0.5; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.25rem;">Sesi Aktif</div>
        <div style="color:#002f45; font-weight:700; font-size:1.1rem;">{{ $qrSession->nama_sesi }}</div>
        @if($qrSession->berlaku_hingga)
        <div style="font-size:0.8rem; color:#d97706; margin-top:0.25rem;">
            ⏰ Berlaku hingga {{ $qrSession->berlaku_hingga->format('H:i') }}
        </div>
        @endif
    </div>

    <form method="POST" action="{{ route('peserta.absen.store') }}" id="absen-form">
        @csrf
        <input type="hidden" name="session_code" value="{{ $qrSession->session_code }}">
        <input type="hidden" name="token"        value="{{ $token ?? '' }}" id="input-token">
        <input type="hidden" name="latitude"     id="input-lat">
        <input type="hidden" name="longitude"    id="input-lng">
        <input type="hidden" name="fingerprint"  id="input-fp">

        <button type="submit" id="btn-absen"
            style="width:100%; padding:1rem; background:#002f45; color:#d2c296; font-weight:700; border:none; border-radius:0.75rem; cursor:pointer; font-size:1rem; opacity:0.5;"
            disabled>
            ⏳ Menunggu lokasi...
        </button>
    </form>

    @else
    {{-- Scanner QR --}}
    <div style="background:white; border-radius:1.25rem; padding:1.5rem; border:2px solid #bdd1d3;">
        <div id="qr-reader" style="width:100%; border-radius:0.75rem; overflow:hidden;"></div>
        <p style="text-align:center; color:#002f45; opacity:0.5; font-size:0.8rem; margin-top:1rem;">
            Arahkan kamera ke QR Code di layar
        </p>
        <div style="display:flex; align-items:center; gap:1rem; margin:1.5rem 0;">
            <div style="flex:1; height:1px; background:#bdd1d3;"></div>
            <span style="color:#002f45; opacity:0.4; font-size:0.75rem;">atau masukkan kode</span>
            <div style="flex:1; height:1px; background:#bdd1d3;"></div>
        </div>
        <form method="GET" action="{{ route('peserta.absen') }}" style="display:flex; gap:0.75rem;">
            <input type="text" name="code" placeholder="Kode sesi (8 digit)"
                style="flex:1; padding:0.75rem 1rem; border:2px solid #bdd1d3; border-radius:0.6rem; font-size:0.9rem; color:#002f45; outline:none; text-transform:uppercase;"
                onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'">
            <button type="submit"
                style="padding:0.75rem 1.25rem; background:#002f45; color:#d2c296; border:none; border-radius:0.6rem; cursor:pointer; font-weight:600; white-space:nowrap;">
                Cek
            </button>
        </form>
    </div>
    @endif

</div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ======= QR SCANNER =======
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('qr-reader')) {
        const scanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 }, false);
        scanner.render(function(decodedText) {
            window.location.href = decodedText;
        });
    }
});

// ======= GEOLOCATION =======
const btnAbsen  = document.getElementById('btn-absen');
const geoText   = document.getElementById('geo-text');
const geoStatus = document.getElementById('geo-status');
const inputLat  = document.getElementById('input-lat');
const inputLng  = document.getElementById('input-lng');

if (btnAbsen && navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            inputLat.value = pos.coords.latitude;
            inputLng.value = pos.coords.longitude;

            geoText.textContent = '📍 Lokasi berhasil didapatkan';
            geoStatus.style.background = '#dcfce7';
            geoStatus.style.borderColor = '#86efac';
            geoStatus.style.color = '#166534';

            enableButton();
        },
        function(err) {
            geoText.textContent = '⚠ Izin lokasi ditolak. Absen tetap bisa dicoba tapi mungkin ditolak sistem.';
            geoStatus.style.background = '#fef9c3';
            geoStatus.style.borderColor = '#fde047';
            geoStatus.style.color = '#854d0e';
            enableButton(); // tetap boleh coba, server yang putuskan
        },
        { timeout: 10000, enableHighAccuracy: true }
    );
} else if (btnAbsen) {
    geoText.textContent = 'Browser tidak mendukung GPS. Pastikan kamu berada di ruangan.';
    enableButton();
}

function enableButton() {
    if (!btnAbsen) return;
    btnAbsen.disabled = false;
    btnAbsen.style.opacity = '1';
    btnAbsen.textContent  = '✓ Konfirmasi Kehadiran Saya';
}

// ======= BROWSER FINGERPRINT =======
async function getFingerprint() {
    const data = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        screen.colorDepth,
        new Date().getTimezoneOffset(),
        navigator.hardwareConcurrency || '',
        navigator.platform || '',
    ].join('|');

    // Hash sederhana pakai Web Crypto API
    const encoder = new TextEncoder();
    const buffer  = await crypto.subtle.digest('SHA-256', encoder.encode(data));
    const hex     = Array.from(new Uint8Array(buffer)).map(b => b.toString(16).padStart(2, '0')).join('');
    return hex;
}

const inputFp = document.getElementById('input-fp');
if (inputFp) {
    getFingerprint().then(fp => { inputFp.value = fp; });
}
</script>
@endsection
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — WTHME</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">
</head>

<body class="min-h-screen flex" style="background-color: #e0decd00; font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- Panel Kiri: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden" style="background-color: #e0decd;">
        {{-- Pattern background --}}
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#bdd1d3" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="relative z-10 flex flex-col items-center justify-center w-full p-12 text-center">
            <div class="flex gap-6 mb-10">

                {{-- Logo Universitas --}}
                <div class="w-20 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/logo-universitas.png') }}" class="w-32 h-32 object-contain">
                </div>

                {{-- Logo Himpunan (BARU) --}}
                <div class="w-20 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/logo-himpunan.png') }}" class="w-28 h-28 object-contain">
                </div>

                {{-- Logo Kegiatan --}}
                <div class="w-20 h-20 flex items-center justify-center">
                    <img src="{{ asset('images/logo-kegiatan.png') }}" class="w-24 h-24 object-contain">
                </div>

            </div>

            <h1
                style="font-family:'Poppins',serif; color:#002f45; font-size:2.5rem; font-weight:700; line-height:1.2; margin-bottom:1rem;">
                WTHME 2025
            </h1>
            <h2
                style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.25rem; font-style:italic; margin-bottom:1.5rem;">
                Welcome To Himpunan Mahasiswa Elektro 2025
            </h2>
            <p style="color:#002f45; font-size:0.875rem; max-width:320px; line-height:1.7;">
                WTHME 2025 adalah kegiatan orientasi mahasiswa baru Teknik Elektro yang bertujuan membentuk karakter
                beretika, loyal, dan memiliki semangat kekeluargaan, sekaligus membantu mahasiswa beradaptasi, mengenal
                lingkungan jurusan, serta mengembangkan potensi diri di bidang akademik dan organisasi.
            </p>

            <div class="mt-12 flex gap-8 text-center">
                {{-- <div>
                    <div style="color:#d2c296; font-size:1.75rem; font-weight:800;">500+</div>
                    <div style="color:#002f45; font-size:0.75rem; opacity:0.7;">Peserta</div>
                </div>
                <div style="width:1px; background:rgba(189,209,211,0.2);"></div>
                <div>
                    <div style="color:#d2c296; font-size:1.75rem; font-weight:800;">50+</div>
                    <div style="color:#002f45; font-size:0.75rem; opacity:0.7;">Panitia</div>
                </div>
                <div style="width:1px; background:rgba(189,209,211,0.2);"></div>
                <div>
                    <div style="color:#d2c296; font-size:1.75rem; font-weight:800;">3</div>
                    <div style="color:#002f45; font-size:0.75rem; opacity:0.7;">Hari</div>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- Panel Kanan: Form Login --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 relative"
        style="
        background-image: linear-gradient(rgba(224, 222, 205, 0.79), rgba(224, 222, 205, 0.659)), url('{{ asset('images/background-login.jpg') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    ">

        <div class="w-full max-w-md relative z-10"> {{-- Z-index agar form tetap di atas --}}

            {{-- Mobile Logo --}}
            <div class="lg:hidden flex justify-center gap-4 mb-8">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:#002f45;">
                    <img src="{{ asset('images/logo-universitas.png') }}" alt=""
                        class="w-10 h-10 object-contain">
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background:#002f45;">
                    <img src="{{ asset('images/logo-kegiatan.png') }}" alt="" class="w-10 h-10 object-contain">
                </div>
            </div>

            <h2
                style="font-family:'Playfair Display',serif; color:#002f45; font-size:2rem; font-weight:700; margin-bottom:0.5rem;">
                Selamat Datang
            </h2>
            <p style="color:#002f45; opacity:0.7; margin-bottom:2rem; font-size:0.9rem; font-weight: 500;">
                Masuk ke akun WTHME kamu
            </p>

            {{-- Form Login dan Error tetap sama di bawah sini --}}
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl" style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b;">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                {{-- Email Input --}}
                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:600; color:#002f45; margin-bottom:0.5rem;">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width:100%; padding:0.875rem 1rem; border:2px solid #bdd1d3; border-radius:0.75rem; 
                           background:rgba(255, 255, 255, 0.9); color:#002f45; font-size:0.9rem; outline:none; transition:border-color 0.2s;
                           box-sizing:border-box;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="email@example.com">
                </div>

                {{-- Password Input --}}
                <div>
                    <label
                        style="display:block; font-size:0.875rem; font-weight:600; color:#002f45; margin-bottom:0.5rem;">
                        Password
                    </label>
                    <input type="password" name="password" required
                        style="width:100%; padding:0.875rem 1rem; border:2px solid #bdd1d3; border-radius:0.75rem; 
                           background:rgba(255, 255, 255, 0.9); color:#002f45; font-size:0.9rem; outline:none; transition:border-color 0.2s;
                           box-sizing:border-box;"
                        onfocus="this.style.borderColor='#002f45'" onblur="this.style.borderColor='#bdd1d3'"
                        placeholder="Password kamu">
                </div>

                <button type="submit"
                    style="width:100%; padding:0.875rem; background:#002f45; color:#d2c296; font-weight:700; 
                       font-size:1rem; border:none; border-radius:0.75rem; cursor:pointer; 
                       transition:background 0.2s; letter-spacing:0.025em;"
                    onmouseover="this.style.background='#00405e'" onmouseout="this.style.background='#002f45'">
                    Masuk
                </button>
            </form>

            <div style="text-align:center; margin-top:1.5rem; color:#002f45; font-weight:500; font-size:0.875rem;">
                Belum punya akun?
                <a href="{{ route('register') }}"
                    style="color:#002f45; font-weight:700; text-decoration:none; border-bottom: 2px solid #d2c296;">
                    Daftar sekarang
                </a>
            </div>
        </div>
    </div>

</body>

</html>

@extends('layouts.app')

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .gantt-container { display: flex; flex-direction: row; gap: 2rem; }
        .calendar-col { flex: 1.6; }
        .list-col { flex: 1; }

        @media (max-width: 1024px) {
            .gantt-container { flex-direction: column; }
            .calendar-col, .list-col { width: 100%; }
            .header-section { flex-direction: column !important; align-items: flex-start !important; gap: 1rem !important; }
            .filter-form { width: 100% !important; justify-content: space-between; }
        }
    </style>

    <div style="min-height:calc(100vh - 64px); padding:1.5rem; background:#e0decd; font-family: 'Inter', sans-serif;">
        <div style="max-width:1200px; margin:0 auto;">

            <a href="{{ route('panitia.index') }}" style="color:#002f45; opacity:0.5; text-decoration:none; font-size:0.875rem; display:block; margin-bottom:1.5rem;">
                ← Kembali
            </a>

            <div class="header-section" style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:2rem; gap:1.5rem;">
                <div>
                    <h1 style="font-family:'Playfair Display',serif; color:#002f45; font-size:2.2rem; font-weight:800; margin:0;">Gantt Chart LKHME</h1>
                    
                    {{-- Section Countdown --}}
                    @if($countdownTarget)
                        <div id="countdownBox" style="margin-top: 15px; background: #002f45; color: #d2c296; padding: 10px 20px; border-radius: 12px; display: inline-flex; align-items: center; gap: 15px; box-shadow: 4px 4px 0px #bdd1d3;">
                            <div style="border-right: 1px solid rgba(210,194,150,0.3); padding-right: 15px;">
                                <div style="font-size: 0.6rem; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8;">Countdown Hari H</div>
                                <div style="font-size: 0.8rem; font-weight: 700; color: #fff;">{{ $namaEventH }}</div>
                            </div>
                            <span id="timerText" style="font-family: 'Courier New', monospace; font-weight: 900; font-size: 1.3rem; letter-spacing: 1px;">Loading...</span>
                        </div>
                    @else
                        <p style="color:#666; margin-top:5px; font-size:0.9rem;">Manajemen timeline kegiatan panitia secara real-time.</p>
                    @endif
                </div>

                {{-- Filter --}}
                <form action="" method="GET" class="filter-form" style="display:flex; gap:10px; align-items:center; background:white; padding:10px 15px; border-radius:12px; border:2px solid #002f45; box-shadow: 4px 4px 0px #002f45;">
                    <div style="display:flex; flex-direction:column;">
                        <label style="font-size:0.6rem; font-weight:900; color:#002f45; text-transform:uppercase;">Bulan Mulai</label>
                        <select name="start" onchange="this.form.submit()" style="border:none; outline:none; font-weight:700; color:#002f45; background:transparent; cursor:pointer;">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('start', date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div style="border-left:2px solid #002f45; height:25px; margin:0 5px;"></div>
                    <div style="display:flex; flex-direction:column;">
                        <label style="font-size:0.6rem; font-weight:900; color:#002f45; text-transform:uppercase;">Tahun</label>
                        <input type="number" name="year" value="{{ $year }}" onchange="this.form.submit()" style="border:none; outline:none; font-weight:700; width:60px; color:#002f45; background:transparent;">
                    </div>
                </form>
            </div>

            <div class="gantt-container">
                {{-- KOLOM KIRI: KALENDER --}}
                <div class="calendar-col">
                    <div style="display:flex; flex-direction:column; gap:2rem;">
                        @foreach ($months as $m)
                            @php
                                $displayYear = request('start') + $loop->index > 12 ? $year + 1 : $year;
                                $dt = \Carbon\Carbon::create($displayYear, $m, 1);
                                $daysInMonth = $dt->daysInMonth;
                                $firstDay = $dt->dayOfWeek;
                            @endphp
                            <div style="background:white; border:2px solid #002f45; border-radius:15px; overflow:hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                                <div style="background:#bdd1d3; color:#002f45; padding:1rem; text-align:center; font-weight:900; border-bottom: 2px solid #002f45;">
                                    {{ $dt->format('F Y') }}
                                </div>
                                <table style="width:100%; border-collapse:collapse; text-align:center;">
                                    <tr style="background:#002f45; color:#ffffff; font-weight:800; font-size:0.75rem;">
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Min</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Sen</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Sel</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Rab</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Kam</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Jum</td>
                                        <td style="padding:10px; border:1px solid #bdd1d3;">Sab</td>
                                    </tr>
                                    <tr>
                                        @for ($i = 0; $i < $firstDay; $i++)
                                            <td style="border:1px solid #bdd1d3; background:#f9f9f9;"></td>
                                        @endfor
                                        @for ($day = 1; $day <= $daysInMonth; $day++)
                                            @php
                                                $curr = \Carbon\Carbon::create($displayYear, $m, $day)->format('Y-m-d');
                                                $event = $kegiatans->filter(fn($k) => $curr >= $k->tanggal_mulai && $curr <= $k->tanggal_selesai)->first();
                                                $bg = 'transparent'; $txt = '#002f45';
                                                if ($event) {
                                                    if ($event->status == 'hijau') $bg = '#70ad47';
                                                    if ($event->status == 'kuning') $bg = '#ffff00';
                                                    if ($event->status == 'merah') { $bg = '#2e5496'; $txt = 'white'; }
                                                }
                                            @endphp
                                            <td onclick="handleDateClick('{{ $curr }}', {{ $event ? json_encode($event) : 'null' }})"
                                                style="padding:1.2rem 0; border:1px solid #bdd1d3; background:{{ $bg }}; color:{{ $txt }}; font-weight:800; cursor:pointer;">
                                                {{ $day }}
                                            </td>
                                            @if (($day + $firstDay) % 7 == 0) </tr><tr> @endif
                                        @endfor
                                    </tr>
                                </table>
                            </div>
                        @endforeach

                        <div style="background:white; border:2px solid #002f45; border-radius:15px; padding:1.2rem; display:flex; gap:1.5rem; justify-content:center; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; gap:8px;"><div style="width:20px; height:20px; background:#ffff00; border:1px solid #000;"></div><span style="font-size:0.8rem; font-weight:700;">Belum</span></div>
                            <div style="display:flex; align-items:center; gap:8px;"><div style="width:20px; height:20px; background:#70ad47; border:1px solid #000;"></div><span style="font-size:0.8rem; font-weight:700;">Sudah</span></div>
                            <div style="display:flex; align-items:center; gap:8px;"><div style="width:20px; height:20px; background:#2e5496; border:1px solid #000;"></div><span style="font-size:0.8rem; font-weight:700;">Hari H</span></div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: LIST AGENDA --}}
                <div class="list-col">
                    <div style="background:#002f45; color:#d2c296; padding:1rem; border-radius:15px 15px 0 0; font-weight:900; text-align:center;">DETAIL AGENDA</div>
                    @foreach ($months as $m)
                        @php
                            $displayYear = request('start') + $loop->index > 12 ? $year + 1 : $year;
                            $eventsMonth = $kegiatans->filter(fn($q) => \Carbon\Carbon::parse($q->tanggal_mulai)->month == $m && \Carbon\Carbon::parse($q->tanggal_mulai)->year == $displayYear);
                        @endphp
                        <div style="background:white; border:2px solid #002f45; border-top:none; margin-bottom:1rem; border-radius: 0 0 15px 15px; overflow:hidden;">
                            <div style="background:#f0f4f5; color:#002f45; padding:0.7rem; text-align:center; font-weight:800; border-bottom:1px solid #bdd1d3; font-size:0.85rem;">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</div>
                            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                                @forelse($eventsMonth as $item)
                                    <tr onclick="handleDateClick('{{ $item->tanggal_mulai }}', {{ json_encode($item) }})" style="cursor:pointer; border-bottom:1px solid #eee;">
                                        <td style="padding:1rem; text-align:center; font-weight:900; width:50px; color:#2e5496; border-right:1px solid #eee;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->day }}</td>
                                        <td style="padding:1rem; font-weight:600; color:#333;">{{ $item->nama_kegiatan }}</td>
                                    </tr>
                                @empty
                                    <tr><td style="padding:2rem; text-align:center; color:#999; font-style:italic;">Tidak ada agenda</td></tr>
                                @endforelse
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div id="modalGantt" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,47,69,0.9); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter: blur(8px);">
        <div style="background:white; padding:2.5rem; border-radius:20px; width:100%; max-width:420px;">
            <h3 id="modalTitle" style="font-family:'Playfair Display',serif; color:#002f45; font-size:1.6rem; margin-bottom:0.5rem;">Update Timeline</h3>
            <p id="displayDate" style="color:#2e5496; font-weight:900; margin-bottom:1.5rem;"></p>
            <form id="ganttForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="tanggal_mulai" id="hidden_mulai">
                <input type="hidden" name="tanggal_selesai" id="hidden_selesai">
                <div id="containerNama" style="margin-bottom:1.2rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:900; color:#002f45; margin-bottom:0.5rem;">NAMA KEGIATAN</label>
                    <input type="text" name="nama_kegiatan" id="in_nama" style="width:100%; padding:0.8rem; border:2px solid #bdd1d3; border-radius:10px; font-weight:600;">
                </div>
                <div style="margin-bottom:2rem;">
                    <label style="display:block; font-size:0.75rem; font-weight:900; color:#002f45; margin-bottom:0.5rem;">STATUS PROGRESS</label>
                    <select name="status" id="in_status" style="width:100%; padding:0.8rem; border:2px solid #bdd1d3; border-radius:10px; font-weight:600;">
                        <option value="kuning">🟡 Belum Terlaksana</option>
                        <option value="hijau">🟢 Sudah Terlaksana</option>
                        <option value="merah">🔵 Hari H Kegiatan</option>
                    </select>
                </div>
                <div style="display:flex; flex-direction:column; gap:0.8rem;">
                    <button type="submit" style="background:#002f45; color:#d2c296; border:none; padding:1rem; border-radius:10px; font-weight:900; cursor:pointer;">SIMPAN</button>
                    <button type="button" id="btnHapus" onclick="submitDelete()" style="display:none; background:#fee2e2; color:#991b1b; border:none; padding:1rem; border-radius:10px; font-weight:900; cursor:pointer;">HAPUS</button>
                    <button type="button" onclick="closeModal()" style="background:#f4f4f4; color:#666; border:none; padding:1rem; border-radius:10px; font-weight:700; cursor:pointer;">BATAL</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        {{-- Script Countdown --}}
        @if($countdownTarget)
        (function() {
            const targetDate = new Date("{{ $countdownTarget }} 00:00:00").getTime();
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = targetDate - now;
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("timerText").innerHTML = "HARI H TIBA!";
                    return;
                }
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById("timerText").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s";
            }, 1000);
        })();
        @endif

        function handleDateClick(date, event) {
            @if (!in_array(auth()->user()->role, ['korlap', 'admin'])) return; @endif
            const modal = document.getElementById('modalGantt');
            modal.style.display = 'flex';
            document.getElementById('displayDate').innerText = "📅 " + date;
            document.getElementById('hidden_mulai').value = date;
            document.getElementById('hidden_selesai').value = date;
            if (event) {
                document.getElementById('modalTitle').innerText = "Update Status";
                document.getElementById('ganttForm').action = "/panitia/gantt/" + event.id;
                document.getElementById('formMethod').value = "PUT";
                document.getElementById('containerNama').style.display = "none";
                document.getElementById('in_status').value = event.status;
                document.getElementById('btnHapus').style.display = "block";
            } else {
                document.getElementById('modalTitle').innerText = "Tambah Agenda";
                document.getElementById('ganttForm').action = "{{ route('panitia.gantt.store') }}";
                document.getElementById('formMethod').value = "POST";
                document.getElementById('containerNama').style.display = "block";
                document.getElementById('in_nama').value = "";
                document.getElementById('in_status').value = "kuning";
                document.getElementById('btnHapus').style.display = "none";
            }
        }

        function submitDelete() {
            if (confirm('Hapus agenda ini secara permanen?')) {
                let f = document.createElement('form');
                f.method = 'POST'; f.action = document.getElementById('ganttForm').action;
                f.innerHTML = '@csrf @method('DELETE')';
                document.body.appendChild(f); f.submit();
            }
        }

        function closeModal() { document.getElementById('modalGantt').style.display = 'none'; }
        window.onclick = function(e) { if (e.target == document.getElementById('modalGantt')) closeModal(); }
    </script>
@endsection
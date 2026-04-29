<?php

namespace App\Http\Controllers;

use App\Models\LinkResource;
use App\Models\QrSession;
use App\Models\AbsensiPeserta;
use App\Models\AbsensiPanitia;
use App\Models\User;
use App\Models\Link;
use App\Models\InformasiPeserta;
use Illuminate\Http\Request;

class PanitiaController extends Controller
{
    public function index()
    {
        $links = LinkResource::where('aktif', true)
            ->orderBy('urutan')
            ->get();

        $qrSessions = QrSession::with('pembuatOleh')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPesertaHadir = AbsensiPeserta::where('status', 'hadir')->count();
        $totalPanitiaHadir = AbsensiPanitia::where('status', 'hadir')->count();

        // 2. Tambahkan variabel baru di bawah ini
        $totalSeluruhPeserta = User::where('role', 'peserta')->count();

        // Menghitung semua panitia (kecuali role peserta)
        $totalSeluruhPanitia = User::whereIn('role', [
            'admin',
            'panitia',
            'korlap',
            'mentor',
            'bendahara',
            'ketuplak'
        ])->count();

        // Ambil semua data link dari database
        $links = \App\Models\Link::all();

        return view('panitia.index', compact(
            'links',
            'qrSessions',
            'totalPesertaHadir',
            'totalPanitiaHadir',
            'totalSeluruhPeserta',
            'totalSeluruhPanitia' // <--- 3. Masukkan ke compact
        ));
    }

    // Tambahkan method ini di dalam class
    public function storeLink(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'url' => 'required|url',
            'ikon' => 'required'
        ]);

        Link::create($request->all());
        return back()->with('success', 'Link berhasil ditambahkan!');
    }

    public function destroyLink($id)
    {
        Link::findOrFail($id)->delete();
        return back()->with('success', 'Link berhasil dihapus!');
    }

    // Fungsi untuk menyimpan pengumuman buat peserta
    public function storeInfoPeserta(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'konten' => 'nullable|string', 
            'url_link' => 'nullable|url',
        ]);

        InformasiPeserta::create($request->all());
        return back()->with('success', 'Pengumuman telah terkirim ke portal peserta!');
    }

    // Fungsi untuk menghapus pengumuman
    public function destroyInfoPeserta($id)
    {
        InformasiPeserta::findOrFail($id)->delete();
        return back()->with('success', 'Pengumuman dihapus.');
    }
    public function indexInfoPeserta()
    {
        $infos = \App\Models\InformasiPeserta::latest()->get();
        return view('panitia.informasi_peserta', compact('infos'));
    }
}

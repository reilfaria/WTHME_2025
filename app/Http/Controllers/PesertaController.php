<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPenyakit;
use App\Models\TugasKategori;
use App\Models\TugasPengumpulan;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index()
    {
        $user            = auth()->user();
        $sudahIsiRiwayat = RiwayatPenyakit::where('user_id', $user->id)->exists();

        // Tugas: berapa yang aktif dan belum dikumpulkan
        $tugasAktif  = TugasKategori::where('aktif', true)->count();
        $sudahKumpul = TugasPengumpulan::where('user_id', $user->id)->count();
        $tugasBelum  = max(0, $tugasAktif - $sudahKumpul);

        return view('peserta.index', compact('user', 'sudahIsiRiwayat', 'tugasAktif', 'tugasBelum'));
    }

    public function riwayatPenyakit()
    {
        $user = auth()->user();
        $data = RiwayatPenyakit::where('user_id', $user->id)->first();
        return view('peserta.riwayat-penyakit', compact('user', 'data'));
    }

    public function simpanRiwayat(Request $request)
    {
        $request->validate([
            'kondisi_kesehatan' => 'required|in:Baik,Cukup,Perlu Perhatian',
        ]);

        $user = auth()->user();

        RiwayatPenyakit::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama'                => $user->name,
                'nim'                 => $user->nim,
                'kelompok'            => $user->kelompok,
                'riwayat_penyakit'    => $request->riwayat_penyakit,
                'alergi'              => $request->alergi,
                'obat_rutin'          => $request->obat_rutin,
                'kondisi_kesehatan'   => $request->kondisi_kesehatan,
                'keterangan_tambahan' => $request->keterangan_tambahan,
            ]
        );

        return redirect()->route('peserta.index')
            ->with('success', 'Data riwayat kesehatan berhasil disimpan!');
    }
}
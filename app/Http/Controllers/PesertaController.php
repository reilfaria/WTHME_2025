<?php

namespace App\Http\Controllers;

use App\Exports\MentoringExport;
use App\Models\MentoringSesi;
use App\Models\MentoringCatatan;
use App\Models\MentorKelompok;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MentoringController extends Controller
{
    // ───────────────────────────────────────────────────────────
    // INDEX — daftar sesi + assign mentor
    // ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = MentoringSesi::with(['mentor', 'catatan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('kelompok');

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        $sesiList     = $query->get();
        $kelompokList = $this->kelompokList();
        $mentorList   = User::whereIn('role', ['panitia', 'admin'])
            ->orderBy('name')
            ->get();

        $assignMap = MentorKelompok::with('mentor')
            ->get()
            ->groupBy('kelompok');

        $stats = [];
        foreach ($kelompokList as $k) {
            $stats[$k] = [
                'sesi'    => MentoringSesi::where('kelompok', $k)->count(),
                'selesai' => MentoringSesi::where('kelompok', $k)->where('selesai', true)->count(),
                'anggota' => User::where('role', 'peserta')->where('kelompok', $k)->count(),
                'mentor'  => $assignMap[$k] ?? collect(),
            ];
        }

        return view('panitia.mentoring.index', compact(
            'sesiList', 'kelompokList', 'mentorList', 'assignMap', 'stats'
        ));
    }

    // ───────────────────────────────────────────────────────────
    // SESI CRUD
    // ───────────────────────────────────────────────────────────

    public function storeSesi(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:255',
            'tanggal'   => 'required|date',
            'kelompok'  => 'required|string',
            'mentor_id' => 'required|exists:users,id',
        ]);

        MentoringSesi::create([
            'judul'       => $request->judul,
            'tanggal'     => $request->tanggal,
            'kelompok'    => $request->kelompok,
            'mentor_id'   => $request->mentor_id,
            'selesai'     => false,
            'dibuat_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Sesi "' . $request->judul . '" untuk Kelompok ' . $request->kelompok . ' berhasil dibuat!');
    }

    public function destroySesi($id)
    {
        MentoringSesi::findOrFail($id)->delete();
        return back()->with('success', 'Sesi berhasil dihapus.');
    }

    // ───────────────────────────────────────────────────────────
    // ASSIGN MENTOR
    // ───────────────────────────────────────────────────────────

    public function assignMentor(Request $request)
    {
        $request->validate([
            'kelompok' => 'required|string',
            'user_id'  => 'required|exists:users,id',
        ]);

        MentorKelompok::firstOrCreate([
            'kelompok' => $request->kelompok,
            'user_id'  => $request->user_id,
        ]);

        return back()->with('success', 'Mentor berhasil di-assign ke Kelompok ' . $request->kelompok . '.');
    }

    public function unassignMentor($id)
    {
        MentorKelompok::findOrFail($id)->delete();
        return back()->with('success', 'Assignment mentor dihapus.');
    }

    // ───────────────────────────────────────────────────────────
    // FORM CATATAN PER SESI
    // ───────────────────────────────────────────────────────────

    public function formCatatan($sesiId)
    {
        $sesi    = MentoringSesi::with(['mentor', 'catatan'])->findOrFail($sesiId);
        $anggota = User::where('role', 'peserta')
            ->where('kelompok', $sesi->kelompok)
            ->orderBy('name')
            ->get();

        $catatanMap   = $sesi->catatan->keyBy('user_id');
        $isMentorSesi = $sesi->mentor_id === auth()->id();

        return view('panitia.mentoring.catatan', compact('sesi', 'anggota', 'catatanMap', 'isMentorSesi'));
    }

    public function simpanCatatan(Request $request, $sesiId)
    {
        $sesi    = MentoringSesi::findOrFail($sesiId);
        $anggota = User::where('role', 'peserta')
            ->where('kelompok', $sesi->kelompok)
            ->get();

        $request->validate([
            'status'       => 'required|array',
            'status.*'     => 'required|in:hadir,izin,alpha',
            'keterangan'   => 'nullable|array',
            'catatan_umum' => 'nullable|string|max:2000',
        ]);

        foreach ($anggota as $p) {
            MentoringCatatan::updateOrCreate(
                ['mentoring_sesi_id' => $sesiId, 'user_id' => $p->id],
                [
                    'nama'       => $p->name,
                    'nim'        => $p->nim,
                    'kelompok'   => $p->kelompok,
                    'status'     => $request->input("status.{$p->id}", 'alpha'),
                    'keterangan' => $request->input("keterangan.{$p->id}"),
                ]
            );
        }

        $sesi->update([
            'catatan_umum' => $request->catatan_umum,
            'selesai'      => true,
        ]);

        return redirect()->route('panitia.mentoring.index')
            ->with('success', 'Catatan sesi "' . $sesi->judul . '" berhasil disimpan!');
    }

    // ───────────────────────────────────────────────────────────
    // REKAP
    // ───────────────────────────────────────────────────────────

    public function rekap(Request $request)
    {
        $filterKelompok = $request->kelompok;
        $kelompokList   = $this->kelompokList();

        $kelompokAda = User::where('role', 'peserta')
            ->distinct()->orderBy('kelompok')->pluck('kelompok');

        $target = $filterKelompok ? collect([$filterKelompok]) : $kelompokAda;

        $data = [];
        foreach ($target as $k) {
            $sesiList = MentoringSesi::where('kelompok', $k)
                ->orderBy('tanggal')->get();

            $anggota = User::where('role', 'peserta')
                ->where('kelompok', $k)->orderBy('name')->get();

            $catatanMap = MentoringCatatan::whereIn('mentoring_sesi_id', $sesiList->pluck('id'))
                ->get()
                ->groupBy('user_id')
                ->map(fn($items) => $items->keyBy('mentoring_sesi_id'));

            $statAnggota = [];
            foreach ($anggota as $p) {
                $cat = $catatanMap[$p->id] ?? collect();
                $statAnggota[$p->id] = [
                    'hadir'      => $cat->where('status', 'hadir')->count(),
                    'izin'       => $cat->where('status', 'izin')->count(),
                    'alpha'      => $cat->where('status', 'alpha')->count(),
                    'total_sesi' => $sesiList->where('selesai', true)->count(),
                ];
            }

            $mentorAssign = MentorKelompok::with('mentor')
                ->where('kelompok', $k)->get();

            $data[$k] = compact('sesiList', 'anggota', 'catatanMap', 'statAnggota', 'mentorAssign');
        }

        return view('panitia.mentoring.rekap', compact('data', 'kelompokList', 'filterKelompok'));
    }

    public function export()
    {
        return Excel::download(
            new MentoringExport(),
            'rekap-mentoring-' . date('Ymd') . '.xlsx'
        );
    }

    // ───────────────────────────────────────────────────────────
    private function kelompokList(): array
    {
        $fromDb = User::where('role', 'peserta')
            ->distinct()->orderBy('kelompok')->pluck('kelompok')->toArray();
        return $fromDb ?: range(1, 20);
    }
}
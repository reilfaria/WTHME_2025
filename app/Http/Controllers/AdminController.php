<?php

namespace App\Http\Controllers;

use App\Imports\PanitiaImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    // Halaman utama panel admin
    public function index()
    {
        $totalPeserta = User::where('role', 'peserta')->count();
        $totalPanitia = User::where('role', 'panitia')->count();
        $panitiaList  = User::where('role', 'panitia')
                            ->orderBy('divisi')
                            ->orderBy('name')
                            ->get();

        return view('admin.index', compact('totalPeserta', 'totalPanitia', 'panitiaList'));
    }

    // Tampilkan form import
    public function importForm()
    {
        return view('admin.import-panitia');
    }

    // Proses upload & import
    public function importStore(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new PanitiaImport();
        Excel::import($import, $request->file('file'));

        $imported = $import->getImported();
        $skipped  = $import->getSkipped();

        $message  = count($imported) . ' akun panitia berhasil dibuat.';
        if (count($skipped) > 0) {
            $message .= ' ' . count($skipped) . ' data dilewati (sudah ada).';
        }

        return redirect()->route('admin.panitia')
            ->with('success', $message)
            ->with('imported', $imported)
            ->with('skipped', $skipped);
    }

    // Hapus akun panitia
    public function deletePanitia($id)
    {
        $user = User::where('id', $id)->where('role', 'panitia')->firstOrFail();
        $user->delete();

        return back()->with('success', 'Akun panitia ' . $user->name . ' berhasil dihapus.');
    }

    // Reset password panitia ke NIM
    public function resetPassword($id)
    {
        $user = User::where('id', $id)->where('role', 'panitia')->firstOrFail();
        $user->update([
            'password'             => bcrypt($user->nim),
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Password ' . $user->name . ' berhasil direset ke NIM.');
    }

    // Edit data panitia
    public function editPanitia($id)
    {
        $panitia = User::where('id', $id)->where('role', 'panitia')->firstOrFail();
        return view('admin.edit-panitia', compact('panitia'));
    }

    public function updatePanitia(Request $request, $id)
    {
        $panitia = User::where('id', $id)->where('role', 'panitia')->firstOrFail();

        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => 'required|string|unique:users,nim,' . $id,
            'angkatan' => 'required|string',
            'divisi'   => 'required|string',
            'email'    => 'required|email|unique:users,email,' . $id,
        ]);

        $panitia->update($request->only('name', 'nim', 'angkatan', 'divisi', 'email'));

        return redirect()->route('admin.panitia')
            ->with('success', 'Data panitia ' . $panitia->name . ' berhasil diperbarui.');
    }

    // Download template Excel
    public function downloadTemplate()
    {
        $path = public_path('templates/template-import-panitia.xlsx');
        return response()->download($path, 'template-import-panitia.xlsx');
    }
}
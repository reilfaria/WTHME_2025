<?php

namespace App\Http\Controllers;

use App\Models\LinkResource;
use App\Models\QrSession;
use App\Models\AbsensiPeserta;
use App\Models\AbsensiPanitia;
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
        
        return view('panitia.index', compact(
            'links', 'qrSessions', 
            'totalPesertaHadir', 'totalPanitiaHadir'
        ));
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pencairan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PencairanController extends Controller
{
    public function store(Request $request, Pengajuan $pengajuan)
    {
        if ($pengajuan->status !== 'disetujui') {
            return response()->json(['message' => 'Pencairan hanya untuk pengajuan disetujui'], 422);
        }
        $data = $request->validate([
            'nomor_doc' => 'required|string',
            'tanggal' => 'required|date',
            'metode' => 'required|string',
            'total_dicairkan' => 'required|integer|min:0',
            'catatan' => 'nullable|string',
        ]);
        $data['pengajuan_id'] = $pengajuan->id;
        $p = Pencairan::create($data);
        $pengajuan->update(['status' => 'dicairkan']);
        return response()->json($p, Response::HTTP_CREATED);
    }
}


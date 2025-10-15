<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $q = Transaksi::query();
        if ($request->filled('from')) $q->where('tanggal', '>=', $request->from);
        if ($request->filled('to')) $q->where('tanggal', '<=', $request->to);
        if ($request->filled('akun_kas')) $q->where('akun_kas', $request->akun_kas);
        return $q->orderByDesc('tanggal')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:debit,kredit',
            'akun_kas' => 'required|string',
            'amount' => 'required|integer|min:0',
            'ref_type' => 'nullable|string',
            'ref_id' => 'nullable|integer',
            'memo' => 'nullable|string',
        ]);
        $t = Transaksi::create($data);
        return response()->json($t, Response::HTTP_CREATED);
    }
}


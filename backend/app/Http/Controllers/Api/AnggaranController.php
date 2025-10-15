<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggaran;
use App\Models\AnggaranItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnggaranController extends Controller
{
    public function index(Request $request)
    {
        $q = Anggaran::query()->withCount('items')->with(['unit','periode']);
        if ($request->filled('unit_id')) $q->where('unit_id', $request->unit_id);
        if ($request->filled('periode_id')) $q->where('periode_id', $request->periode_id);
        return $q->orderByDesc('id')->paginate(20);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'periode_id' => 'required|exists:periodes,id',
            'total_pagu' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        $validated['status'] = 'draft';
        $a = Anggaran::create($validated);
        return response()->json($a, Response::HTTP_CREATED);
    }

    public function show(Anggaran $anggaran)
    {
        return $anggaran->load('items');
    }


    public function addItem(Request $request, Anggaran $anggaran)
    {
        $data = $request->validate([
            'account_code' => 'required|string',
            'description' => 'required|string',
            'pagu' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);
        $data['anggaran_id'] = $anggaran->id;
        $item = AnggaranItem::create($data);
        return response()->json($item, Response::HTTP_CREATED);
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $validated = $request->validate([
            'total_pagu' => 'sometimes|integer|min:0',
            'notes' => 'nullable|string',
            'status' => 'in:draft,final',
        ]);
        $anggaran->update($validated);
        return $anggaran->refresh();
    }

    public function finalize(Anggaran $anggaran)
    {
        $anggaran->update(['status' => 'final']);
        return $anggaran;
    }
}

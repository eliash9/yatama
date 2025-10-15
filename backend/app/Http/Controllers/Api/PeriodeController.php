<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodeController extends Controller
{
    public function index()
    {
        return Periode::query()->orderByDesc('start_date')->paginate(20);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:periodes,code',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_locked' => 'boolean',
        ]);
        $p = Periode::create($validated);
        return response()->json($p, Response::HTTP_CREATED);
    }

    public function show(Periode $periode)
    {
        return $periode;
    }

    public function update(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|unique:periodes,code,' . $periode->id,
            'name' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'is_locked' => 'boolean',
        ]);
        $periode->update($validated);
        return $periode->refresh();
    }

    public function destroy(Periode $periode)
    {
        $periode->delete();
        return response()->noContent();
    }
}

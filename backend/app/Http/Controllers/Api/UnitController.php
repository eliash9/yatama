<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
{
    public function index()
    {
        return Unit::query()->orderBy('name')->paginate(20);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:units,code',
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:units,id',
            'is_active' => 'boolean',
        ]);

        $unit = Unit::create($validated);
        return response()->json($unit, Response::HTTP_CREATED);
    }

    public function show(Unit $unit)
    {
        return $unit;
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|unique:units,code,' . $unit->id,
            'name' => 'sometimes|string',
            'parent_id' => 'nullable|exists:units,id',
            'is_active' => 'boolean',
        ]);
        $unit->update($validated);
        return $unit->refresh();
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $q = Program::query()->with('unit');
        if ($request->filled('type')) $q->where('type', $request->type);
        if ($request->filled('status')) $q->where('status', $request->status);
        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $q->where(function($qq) use ($s){
                $qq->where('name','like',$s)->orWhere('code','like',$s)->orWhere('category','like',$s);
            });
        }
        return $q->orderBy('name')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:programs,code',
            'name' => 'required|string',
            'category' => 'nullable|string',
            'type' => 'nullable|in:program,campaign',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_amount' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);
        $row = Program::create($data);
        return response()->json($row, Response::HTTP_CREATED);
    }

    public function show(Program $program) { return $program->load('unit'); }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'code' => 'sometimes|string|unique:programs,code,'.$program->id,
            'name' => 'sometimes|string',
            'category' => 'nullable|string',
            'type' => 'nullable|in:program,campaign',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_amount' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);
        $program->update($data);
        return $program->refresh();
    }

    public function destroy(Program $program){ $program->delete(); return response()->noContent(); }
}


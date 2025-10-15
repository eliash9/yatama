<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProgramPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Program::query()->with('unit');
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('name','like',$like)->orWhere('code','like',$like)->orWhere('category','like',$like);
            });
        }
        if ($t = $request->query('type')) { $q->where('type',$t); }
        if ($st = $request->query('status')) { $q->where('status',$st); }
        $rows = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('master.programs.index', compact('rows'));
    }

    public function create()
    {
        $row = new Program();
        $units = Unit::orderBy('name')->get(['id','name']);
        return view('master.programs.form', compact('row','units'));
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
        Program::create($data);
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye dibuat');
    }

    public function edit(Program $program)
    {
        $row = $program;
        $units = Unit::orderBy('name')->get(['id','name']);
        return view('master.programs.form', compact('row','units'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:programs,code,'.$program->id,
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
        $program->update($data);
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye diubah');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye dihapus');
    }
}


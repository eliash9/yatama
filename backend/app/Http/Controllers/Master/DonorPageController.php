<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonorPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Donor::query();
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('name','like',$like)->orWhere('code','like',$like)->orWhere('email','like',$like);
            });
        }
        if ($t = $request->query('type')) { $q->where('type',$t); }
        $rows = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('master.donors.index', compact('rows'));
    }

    public function create()
    {
        $row = new Donor();
        return view('master.donors.form', compact('row'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:donors,code',
            'type' => 'required|in:individual,company',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        Donor::create($data);
        return redirect()->route('master.donors.index')->with('status','Donatur dibuat');
    }

    public function edit(Donor $donor)
    {
        $row = $donor;
        return view('master.donors.form', compact('row'));
    }

    public function update(Request $request, Donor $donor)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:donors,code,'.$donor->id,
            'type' => 'required|in:individual,company',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $donor->update($data);
        return redirect()->route('master.donors.index')->with('status','Donatur diubah');
    }

    public function destroy(Donor $donor)
    {
        $donor->delete();
        return redirect()->route('master.donors.index')->with('status','Donatur dihapus');
    }

    public function show(Donor $donor)
    {
        $rows = \App\Models\Income::with('program')->where('donor_id',$donor->id)->orderByDesc('tanggal')->paginate(15);
        $total = \App\Models\Income::where('donor_id',$donor->id)->sum('amount');
        $byProgram = \App\Models\Income::select('program_id', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->where('donor_id',$donor->id)->groupBy('program_id')->get();
        $programNames = \App\Models\Program::whereIn('id',$byProgram->pluck('program_id')->filter())->pluck('name','id');
        return view('master.donors.show', compact('donor','rows','total','byProgram','programNames'));
    }
}

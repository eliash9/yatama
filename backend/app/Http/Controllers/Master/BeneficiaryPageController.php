<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Beneficiary::query();
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('name','like',$like)->orWhere('code','like',$like)->orWhere('guardian_name','like',$like);
            });
        }
        if ($t = $request->query('type')) { $q->where('type',$t); }
        $rows = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('master.beneficiaries.index', compact('rows'));
    }

    public function create()
    {
        $row = new Beneficiary();
        return view('master.beneficiaries.form', compact('row'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:beneficiaries,code',
            'type' => 'required|string',
            'name' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        Beneficiary::create($data);
        return redirect()->route('master.beneficiaries.index')->with('status','Penerima dibuat');
    }

    public function edit(Beneficiary $beneficiary)
    {
        $row = $beneficiary;
        return view('master.beneficiaries.form', compact('row'));
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:beneficiaries,code,'.$beneficiary->id,
            'type' => 'required|string',
            'name' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $beneficiary->update($data);
        return redirect()->route('master.beneficiaries.index')->with('status','Penerima diubah');
    }

    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();
        return redirect()->route('master.beneficiaries.index')->with('status','Penerima dihapus');
    }
}


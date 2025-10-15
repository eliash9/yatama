<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BeneficiaryController extends Controller
{
    public function index(Request $request)
    {
        $q = Beneficiary::query();
        if ($request->filled('type')) $q->where('type', $request->type);
        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $q->where(function($qq) use ($s){
                $qq->where('name','like',$s)->orWhere('guardian_name','like',$s)->orWhere('phone','like',$s)->orWhere('code','like',$s);
            });
        }
        return $q->orderBy('name')->paginate(20);
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
            'is_active' => 'boolean',
        ]);
        $row = Beneficiary::create($data);
        return response()->json($row, Response::HTTP_CREATED);
    }

    public function show(Beneficiary $beneficiary) { return $beneficiary; }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $data = $request->validate([
            'code' => 'sometimes|string|unique:beneficiaries,code,'.$beneficiary->id,
            'type' => 'sometimes|string',
            'name' => 'sometimes|string',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $beneficiary->update($data);
        return $beneficiary->refresh();
    }

    public function destroy(Beneficiary $beneficiary){ $beneficiary->delete(); return response()->noContent(); }
}


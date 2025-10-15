<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        $q = Donor::query();
        if ($request->filled('type')) $q->where('type', $request->type);
        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $q->where(function($qq) use ($s){
                $qq->where('name','like',$s)->orWhere('email','like',$s)->orWhere('phone','like',$s)->orWhere('code','like',$s);
            });
        }
        return $q->orderBy('name')->paginate(20);
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
            'is_active' => 'boolean',
        ]);
        $d = Donor::create($data);
        return response()->json($d, Response::HTTP_CREATED);
    }

    public function show(Donor $donor) { return $donor; }

    public function update(Request $request, Donor $donor)
    {
        $data = $request->validate([
            'code' => 'sometimes|string|unique:donors,code,'.$donor->id,
            'type' => 'sometimes|in:individual,company',
            'name' => 'sometimes|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $donor->update($data);
        return $donor->refresh();
    }

    public function destroy(Donor $donor){ $donor->delete(); return response()->noContent(); }
}


<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VolunteerController extends Controller
{
    public function index(Request $request)
    {
        $q = Volunteer::query();
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
            'code' => 'required|string|unique:volunteers,code',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'skills' => 'nullable|string',
            'joined_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);
        $row = Volunteer::create($data);
        return response()->json($row, Response::HTTP_CREATED);
    }

    public function show(Volunteer $volunteer) { return $volunteer; }

    public function update(Request $request, Volunteer $volunteer)
    {
        $data = $request->validate([
            'code' => 'sometimes|string|unique:volunteers,code,'.$volunteer->id,
            'name' => 'sometimes|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'skills' => 'nullable|string',
            'joined_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);
        $volunteer->update($data);
        return $volunteer->refresh();
    }

    public function destroy(Volunteer $volunteer){ $volunteer->delete(); return response()->noContent(); }
}


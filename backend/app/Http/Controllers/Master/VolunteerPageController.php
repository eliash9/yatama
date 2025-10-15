<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Volunteer::query();
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('name','like',$like)->orWhere('code','like',$like)->orWhere('email','like',$like);
            });
        }
        $rows = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('master.volunteers.index', compact('rows'));
    }

    public function create()
    {
        $row = new Volunteer();
        return view('master.volunteers.form', compact('row'));
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
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        Volunteer::create($data);
        return redirect()->route('master.volunteers.index')->with('status','Relawan dibuat');
    }

    public function edit(Volunteer $volunteer)
    {
        $row = $volunteer;
        return view('master.volunteers.form', compact('row'));
    }

    public function update(Request $request, Volunteer $volunteer)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:volunteers,code,'.$volunteer->id,
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'skills' => 'nullable|string',
            'joined_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $volunteer->update($data);
        return redirect()->route('master.volunteers.index')->with('status','Relawan diubah');
    }

    public function destroy(Volunteer $volunteer)
    {
        $volunteer->delete();
        return redirect()->route('master.volunteers.index')->with('status','Relawan dihapus');
    }
}


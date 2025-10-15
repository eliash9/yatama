<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function ($qq) use ($like) {
                $qq->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }
        $users = $q->orderBy('name')->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get(['id','name']);
        $userRoles = $user->roles->pluck('name')->all();
        return view('admin.users.edit', compact('user','roles','userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => 'array',
            'roles.*' => 'string'
        ]);
        $roles = $data['roles'] ?? [];
        $user->syncRoles($roles);
        return redirect()->route('admin.users.index')->with('status','Peran pengguna diperbarui');
    }
}


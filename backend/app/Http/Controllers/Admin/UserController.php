<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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

    public function create()
    {
        $roles = Role::orderBy('name')->get(['id','name']);
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'string',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        if (!empty($data['roles'])) { $user->syncRoles($data['roles']); }
        return redirect()->route('admin.users.index')->with('status','Pengguna ditambahkan');
    }
}

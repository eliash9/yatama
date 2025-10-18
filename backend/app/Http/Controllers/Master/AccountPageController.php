<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Account::query();
        if ($t = $request->query('type')) $q->where('type',$t);
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('code','like',$like)->orWhere('name','like',$like)->orWhere('account_no','like',$like);
            });
        }
        // Urut berdasarkan kode akun agar mengikuti struktur COA
        $rows = $q->orderBy('code')->paginate(15)->withQueryString();
        return view('master.accounts.index', compact('rows'));
    }

    public function create(){ $row = new Account(); return view('master.accounts.form', compact('row')); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:accounts,code',
            'name' => 'required|string',
            'type' => 'required|in:cash,bank',
            'bank_name' => 'nullable|string',
            'account_no' => 'nullable|string',
            'opening_balance' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        Account::create($data);
        return redirect()->route('master.accounts.index')->with('status','Akun dibuat');
    }

    public function edit(Account $account){ $row = $account; return view('master.accounts.form', compact('row')); }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:accounts,code,'.$account->id,
            'name' => 'required|string',
            'type' => 'required|in:cash,bank',
            'bank_name' => 'nullable|string',
            'account_no' => 'nullable|string',
            'opening_balance' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $account->update($data);
        return redirect()->route('master.accounts.index')->with('status','Akun diubah');
    }

    public function destroy(Account $account){ $account->delete(); return redirect()->route('master.accounts.index')->with('status','Akun dihapus'); }
}

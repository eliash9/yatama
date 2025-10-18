<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransactionPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Transaksi::query();
        if ($request->filled('from')) $q->where('tanggal','>=',$request->from);
        if ($request->filled('to')) $q->where('tanggal','<=',$request->to);
        if ($request->filled('akun_kas')) $q->where('akun_kas',$request->akun_kas);
        if ($request->filled('jenis')) $q->where('jenis',$request->jenis);
        $rows = $q->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('finance.transactions.index', compact('rows'));
    }

    public function create()
    {
        $row = new Transaksi(['tanggal'=>date('Y-m-d')]);
        $accounts = \App\Models\Account::where('is_active',true)->orderBy('name')->get(['id','name','code']);
        $programs = \App\Models\Program::orderBy('name')->get(['id','name']);
        return view('finance.transactions.form', compact('row','accounts','programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:debit,kredit',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|integer|min:0',
            'program_id' => 'nullable|exists:programs,id',
            'category' => 'nullable|in:operational',
            'memo' => 'nullable|string',
        ]);
        $acc = \App\Models\Account::find($data['account_id']);
        $data['akun_kas'] = $acc->code; // fallback label
        Transaksi::create($data);
        return redirect()->route('finance.transactions.index')->with('status','Transaksi dicatat');
    }

    public function destroy(Transaksi $transaction)
    {
        $transaction->delete();
        return redirect()->route('finance.transactions.index')->with('status','Transaksi dihapus');
    }
}

<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashbookPageController extends Controller
{
    public function __invoke(Request $request)
    {
        $accountId = $request->query('account_id');
        $from = $request->query('from');
        $to = $request->query('to');
        $accounts = Account::orderBy('name')->get(['id','name','code']);
        $rows = collect();
        $opening = null; $account = null;
        if ($accountId) {
            $account = Account::find($accountId);
            $q = Transaksi::where('account_id',$accountId);
            if ($from) $q->where('tanggal','>=',$from); if ($to) $q->where('tanggal','<=',$to);
            $rows = $q->orderBy('tanggal')->orderBy('id')->get();
            $prior = Transaksi::where('account_id',$accountId)
                ->when($from, fn($qq)=>$qq->where('tanggal','<',$from))
                ->select(DB::raw("SUM(CASE WHEN jenis='debit' THEN amount ELSE -amount END) as net"))->value('net') ?? 0;
            $opening = (int) $account->opening_balance + (int) $prior;
        }
        return view('finance.cashbook.index', compact('accounts','account','rows','opening','from','to'));
    }
}


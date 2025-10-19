<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\Program;

class DonorPortalController extends Controller
{
    private function requireAuth()
    {
        if (!session('donor_id')) abort(403);
        return (int) session('donor_id');
    }

    public function dashboard(Request $request)
    {
        $donorId = $this->requireAuth();
        $recent = Income::where('donor_id',$donorId)->orderByDesc('tanggal')->limit(10)->get();
        $total = (int) Income::where('donor_id',$donorId)->sum('amount');
        $byProgram = Income::select(DB::raw('COALESCE(program_id,0) as pid'), DB::raw('SUM(amount) as total'))
            ->where('donor_id',$donorId)->groupBy('pid')->get();
        $programNames = Program::whereIn('id', $byProgram->pluck('pid')->filter())->pluck('name','id');
        $donor = \App\Models\Donor::findOrFail($donorId);
        return view('public.donor.dashboard', compact('recent','total','byProgram','programNames','donor'));
    }

    public function donations(Request $request)
    {
        $donorId = $this->requireAuth();
        $rows = Income::where('donor_id',$donorId)->orderByDesc('tanggal')->paginate(10);
        return view('public.donor.donations', compact('rows'));
    }

    public function reports(Request $request)
    {
        $donorId = $this->requireAuth();
        // Ringkasan pengeluaran program global vs donasi donor per program
        $byProgramDonor = Income::select(DB::raw('COALESCE(program_id,0) as pid'), DB::raw('SUM(amount) as total'))
            ->where('donor_id',$donorId)->groupBy('pid')->get();
        $spendByProgram = \App\Models\Transaksi::whereNotNull('program_id')
            ->select('program_id', DB::raw("SUM(CASE WHEN jenis='kredit' THEN amount ELSE 0 END) as total"))
            ->groupBy('program_id')->pluck('total','program_id');
        $programs = Program::orderBy('name')->get(['id','name']);
        return view('public.donor.reports', compact('byProgramDonor','spendByProgram','programs'));
    }

    public function claim(Request $request)
    {
        $donorId = $this->requireAuth();
        $data = $request->validate(['receipt_no'=>'required|string']);
        $inc = Income::where('receipt_no',$data['receipt_no'])->first();
        if (!$inc) return back()->withErrors(['receipt_no'=>'Kwitansi tidak ditemukan']);
        if ($inc->donor_id && $inc->donor_id !== $donorId) return back()->withErrors(['receipt_no'=>'Kwitansi sudah terhubung ke akun lain']);
        $inc->update(['donor_id'=>$donorId]);
        return back()->with('status','Donasi berhasil ditautkan');
    }
}




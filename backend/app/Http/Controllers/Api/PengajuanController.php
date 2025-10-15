<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $q = Pengajuan::query()->with(['unit','periode','pemohon'])->withCount('items');
        if ($request->filled('unit_id')) $q->where('unit_id', $request->unit_id);
        if ($request->filled('periode_id')) $q->where('periode_id', $request->periode_id);
        if ($request->filled('status')) $q->where('status', $request->status);

        $list = $q->orderByDesc('id')->paginate(20);

        $summary = Pengajuan::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_diminta) as total'))
            ->when($request->filled('unit_id'), fn($qq) => $qq->where('unit_id', $request->unit_id))
            ->when($request->filled('periode_id'), fn($qq) => $qq->where('periode_id', $request->periode_id))
            ->groupBy('status')->get();

        return response()->json([
            'data' => $list->items(),
            'meta' => [
                'current_page' => $list->currentPage(),
                'last_page' => $list->lastPage(),
                'per_page' => $list->perPage(),
                'total' => $list->total(),
            ],
            'summary' => $summary,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'periode_id' => 'required|exists:periodes,id',
            'judul' => 'required|string',
            'deskripsi' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|integer|min:0',
            'items.*.anggaran_item_id' => 'nullable|exists:anggaran_items,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $kode = 'PD-'.date('Y').'-'.strtoupper(Str::random(6));
            $pengajuan = Pengajuan::create([
                'kode' => $kode,
                'unit_id' => $validated['unit_id'],
                'periode_id' => $validated['periode_id'],
                'pemohon_id' => $request->user()->id,
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status' => 'draft',
            ]);

            $total = 0;
            foreach ($validated['items'] as $it) {
                $subtotal = (int)($it['unit_price']) * (float)($it['qty']);
                $total += (int) $subtotal;
                PengajuanItem::create([
                    'pengajuan_id' => $pengajuan->id,
                    'account_code' => $it['account_code'],
                    'description' => $it['description'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'subtotal' => (int) $subtotal,
                    'anggaran_item_id' => $it['anggaran_item_id'] ?? null,
                ]);
            }
            $pengajuan->update(['total_diminta' => $total]);

            return response()->json($pengajuan->load('items'), Response::HTTP_CREATED);
        });
    }

    public function show(Pengajuan $pengajuan)
    {
        return $pengajuan->load(['items','approvals','pencairans']);
    }


    public function update(Request $request, Pengajuan $pengajuan)
    {
        if (!in_array($pengajuan->status, ['draft','diajukan'])) {
            return response()->json(['message' => 'Tidak bisa mengubah pada status ini'], 422);
        }
        $validated = $request->validate([
            'judul' => 'sometimes|string',
            'deskripsi' => 'nullable|string',
            'items' => 'nullable|array|min:1',
        ]);

        $pengajuan->update(array_filter($validated, fn($k) => in_array($k, ['judul','deskripsi']), ARRAY_FILTER_USE_KEY));

        if (!empty($validated['items'])) {
            $pengajuan->items()->delete();
            $total = 0; 
            foreach ($validated['items'] as $it) {
                $subtotal = (int)($it['unit_price']) * (float)($it['qty']);
                $total += (int) $subtotal;
                PengajuanItem::create([
                    'pengajuan_id' => $pengajuan->id,
                    'account_code' => $it['account_code'],
                    'description' => $it['description'],
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'subtotal' => (int) $subtotal,
                    'anggaran_item_id' => $it['anggaran_item_id'] ?? null,
                ]);
            }
            $pengajuan->update(['total_diminta' => $total]);
        }

        return $pengajuan->load('items');
    }

    public function submit(Pengajuan $pengajuan)
    {
        if ($pengajuan->status !== 'draft') {
            return response()->json(['message' => 'Hanya draft yang bisa disubmit'], 422);
        }
        if ($pengajuan->items()->count() < 1) {
            return response()->json(['message' => 'Minimal 1 item diperlukan'], 422);
        }
        $pengajuan->update(['status' => 'diajukan', 'submitted_at' => now()]);
        return $pengajuan;
    }

    public function decide(Request $request, Pengajuan $pengajuan)
    {
        $data = $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string'
        ]);
        if (!in_array($pengajuan->status, ['diajukan','ditinjau'])) {
            return response()->json(['message' => 'Status pengajuan tidak valid untuk keputusan'], 422);
        }

        $level = (int) ($pengajuan->approvals()->max('level') ?? 0) + 1;
        $status = $data['action'] === 'approve' ? 'approved' : 'rejected';
        $approval = Approval::create([
            'pengajuan_id' => $pengajuan->id,
            'approver_id' => $request->user()->id,
            'level' => $level,
            'status' => $status,
            'note' => $data['note'] ?? null,
            'decided_at' => now(),
        ]);

        if ($status === 'approved') {
            $pengajuan->update(['status' => 'disetujui']);
        } else {
            $pengajuan->update(['status' => 'ditolak']);
        }
        return $pengajuan->load('approvals');
    }
}

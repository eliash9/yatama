<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Unit;
use Illuminate\Http\Request;

class ProgramPageController extends Controller
{
    public function index(Request $request)
    {
        $q = Program::query()->with('unit');
        if ($s = $request->query('search')) {
            $like = "%$s%";
            $q->where(function($qq) use ($like){
                $qq->where('name','like',$like)->orWhere('code','like',$like)->orWhere('category','like',$like);
            });
        }
        if ($t = $request->query('type')) { $q->where('type',$t); }
        if ($st = $request->query('status')) { $q->where('status',$st); }
        $rows = $q->orderBy('name')->paginate(10)->withQueryString();
        return view('master.programs.index', compact('rows'));
    }

    public function create()
    {
        $row = new Program();
        $units = Unit::orderBy('name')->get(['id','name']);
        return view('master.programs.form', compact('row','units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:programs,code',
            'name' => 'required|string',
            'category' => 'nullable|string',
            'type' => 'nullable|in:program,campaign',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'banner' => 'nullable|image|max:4096,ratio=3/1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_amount' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'program-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(10)).'.jpg';
            $path = 'program-banners/'.$filename;
            $this->processBannerToJpeg($file->getPathname(), 1200, 400, 80, \Illuminate\Support\Facades\Storage::disk('public')->path($path));
            $data['banner_url'] = 'storage/'.$path;
        }
        unset($data['banner']);
        Program::create($data);
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye dibuat');
    }

    public function edit(Program $program)
    {
        $row = $program;
        $units = Unit::orderBy('name')->get(['id','name']);
        return view('master.programs.form', compact('row','units'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:programs,code,'.$program->id,
            'name' => 'required|string',
            'category' => 'nullable|string',
            'type' => 'nullable|in:program,campaign',
            'unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'banner' => 'nullable|image|max:4096,ratio=3/1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_amount' => 'nullable|integer|min:0',
            'status' => 'nullable|string',
        ]);
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $filename = 'program-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(10)).'.jpg';
            $path = 'program-banners/'.$filename;
            $this->processBannerToJpeg($file->getPathname(), 1200, 400, 80, \Illuminate\Support\Facades\Storage::disk('public')->path($path));
            $data['banner_url'] = 'storage/'.$path;
        }
        unset($data['banner']);
        $program->update($data);
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye diubah');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('master.programs.index')->with('status','Program/Kampanye dihapus');
    }

    private function processBannerToJpeg(string $srcPath, int $targetW, int $targetH, int $quality, string $destPath): void
    {
        $info = @getimagesize($srcPath);
        if (!$info) { return; }
        $mime = $info['mime'] ?? '';
        $srcImg = null;
        if ($mime === 'image/jpeg') { $srcImg = @imagecreatefromjpeg($srcPath); }
        elseif ($mime === 'image/png') { $srcImg = @imagecreatefrompng($srcPath); }
        elseif ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) { $srcImg = @imagecreatefromwebp($srcPath); }
        if (!$srcImg) { return; }
        $srcW = imagesx($srcImg); $srcH = imagesy($srcImg);
        $targetRatio = $targetW / max(1,$targetH); $srcRatio = $srcW / max(1,$srcH);
        if ($srcRatio > $targetRatio) { $newW = (int)($srcH * $targetRatio); $newH = $srcH; $srcX = (int)(($srcW - $newW)/2); $srcY = 0; }
        else { $newW = $srcW; $newH = (int)($srcW / $targetRatio); $srcX = 0; $srcY = (int)(($srcH - $newH)/2); }
        $dst = imagecreatetruecolor($targetW, $targetH);
        imagecopyresampled($dst, $srcImg, 0, 0, $srcX, $srcY, $targetW, $targetH, $newW, $newH);
        @imagejpeg($dst, $destPath, $quality);
        imagedestroy($dst); imagedestroy($srcImg);
    }
}

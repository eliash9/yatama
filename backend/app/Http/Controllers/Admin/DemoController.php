<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\RedirectResponse;

class DemoController extends Controller
{
    public function reset(): RedirectResponse
    {
        Artisan::call('app:demo-data', ['--fresh' => true]);
        return back()->with('status','Demo data direset.');
    }
}


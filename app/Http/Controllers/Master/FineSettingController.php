<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\FineSetting;
use Illuminate\Http\Request;

class FineSettingController extends Controller
{
    public function index()
    {
        $fineSetting = FineSetting::first() ?? new FineSetting([
            'daily_fine' => 0,
            'lost_book_fine' => 0,
            'lost_fine_type' => 'flat',
        ]);

        return view('master.fine-settings.index', [
            'fineSetting' => $fineSetting,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_fine' => 'required|numeric|min:0',
            'lost_book_fine' => 'required|numeric|min:0',
            'lost_fine_type' => 'required|in:flat,book_price',
        ]);

        $fineSetting = FineSetting::first();

        if ($fineSetting) {
            $fineSetting->update($validated);
        } else {
            FineSetting::create($validated);
        }

        return redirect()
            ->route('master.fine-settings.index')
            ->with('success', 'Pengaturan denda berhasil disimpan.');
    }
}

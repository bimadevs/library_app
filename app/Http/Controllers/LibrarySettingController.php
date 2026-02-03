<?php

namespace App\Http\Controllers;

use App\Models\LibrarySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibrarySettingController extends Controller
{
    public function index()
    {
        $setting = LibrarySetting::firstOrFail();
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = LibrarySetting::firstOrFail();

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_logo' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($request->hasFile('school_logo')) {
            // Delete old logo
            if ($setting->school_logo && Storage::disk('public')->exists($setting->school_logo)) {
                Storage::disk('public')->delete($setting->school_logo);
            }

            $path = $request->file('school_logo')->store('settings', 'public');
            $validated['school_logo'] = $path;
        }

        $setting->update($validated);

        return back()->with('success', 'Pengaturan identitas berhasil diperbarui.');
    }
}

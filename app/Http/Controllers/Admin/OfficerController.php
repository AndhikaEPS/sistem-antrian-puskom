<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    public function index()
    {
        $officers = Officer::with(['user', 'currentService'])->orderBy('counter_number')->paginate(15);

        return view('admin.officers', compact('officers'));
    }

    public function update(Request $request, Officer $officer): RedirectResponse
    {
        $validated = $request->validate([
            'counter_number' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:available,busy,offline'],
        ]);

        $officer->update($validated);

        return back()->with('success', 'Data petugas diperbarui.');
    }
}

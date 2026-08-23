<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->paginate(15);

        return view('admin.services', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_code' => ['required', 'string', 'max:5', 'unique:services,service_code'],
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        Service::create($validated + ['status' => 'active']);

        return back()->with('success', 'Jenis layanan berhasil ditambahkan.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'service_code' => ['required', 'string', 'max:5', Rule::unique('services', 'service_code')->ignore($service->id)],
            'service_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'estimated_duration' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $service->update($validated);

        return back()->with('success', 'Jenis layanan diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->queues()->exists()) {
            $service->update(['status' => 'inactive']);

            return back()->with('warning', 'Layanan memiliki riwayat antrian, dinonaktifkan saja (tidak dihapus permanen).');
        }

        $service->delete();

        return back()->with('success', 'Jenis layanan dihapus.');
    }
}

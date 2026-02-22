<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceSettingsController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403,
            'Unauthorized.'
        );
    }

    public function index()
    {
        $this->ensureAdmin();

        $maintenances = Maintenance::query()
            ->whereIn('platform', ['web', 'mobile'])
            ->get()
            ->keyBy('platform');

        return view('admin.settings.maintenance', compact('maintenances'));
    }

    public function upsert(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'platform' => ['required', 'in:mobile,web'],
            'maintenance' => ['nullable', 'boolean'],
            'message' => ['required', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
        ]);

        Maintenance::query()->updateOrCreate(
            ['platform' => $validated['platform']],
            [
                'maintenance' => $request->boolean('maintenance'),
                'message' => $validated['message'],
                'start_at' => $validated['start_at'],
                'end_at' => $validated['end_at'],
            ]
        );

        return redirect()
            ->route('admin.settings.maintenance')
            ->with('success', ucfirst($validated['platform']) . ' maintenance settings updated successfully.');
    }
}

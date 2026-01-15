<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomerAppliedJob;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobPost::where('owner_id', Auth::id())
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('provider.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('provider.jobs.create', compact('parentCategories'));
    }

    public function show(JobPost $job)
    {
        if ($job->owner_id !== Auth::id()) {
            abort(403);
        }

        $job->load('category');

        return view('provider.jobs.show', compact('job'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'nullable|integer',
            'nice_to_have' => 'nullable|string',
            'deadline' => 'nullable|date',
            'type' => 'required|string',
            'type_other' => 'nullable|string|max:255',
            'onsite' => 'nullable|boolean',
            'location' => 'required|string|max:255',
        ]);

        $typeId = null;
        $typeOther = null;

        if ($validated['type'] === 'other') {
            $request->validate([
                'type_other' => 'required|string|max:255',
            ]);
            $typeOther = $validated['type_other'];
        } else {
            $request->validate([
                'type' => 'exists:categories,id',
            ]);
            $typeId = (int) $validated['type'];
        }

        JobPost::create([
            'owner_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'salary' => $validated['salary'] ?? null,
            'nice_to_have' => $validated['nice_to_have'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'type' => $typeId,
            'type_other' => $typeOther,
            'number_of_applications' => 0,
            'onsite' => $request->boolean('onsite'),
            'location' => $validated['location'],
        ]);

        return redirect()->route('provider.jobs.index')->with('success', 'Job posted successfully.');
    }

    public function destroy(JobPost $job)
    {
        if ($job->owner_id !== Auth::id()) {
            abort(403);
        }

        $job->delete();

        return redirect()->route('provider.jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function applicants(JobPost $job)
    {
        if ($job->owner_id !== Auth::id()) {
            abort(403);
        }

        $applications = CustomerAppliedJob::where('job_id', $job->id)
            ->latest()
            ->get();

        return view('provider.jobs.applicants', compact('job', 'applications'));
    }

    public function applicantCv(CustomerAppliedJob $application)
    {
        $job = JobPost::findOrFail($application->job_id);

        if ($job->owner_id !== Auth::id()) {
            abort(403);
        }

        $path = $application->user_cv;
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }

    public function destroyApplication(CustomerAppliedJob $application)
    {
        $job = JobPost::findOrFail($application->job_id);

        if ($job->owner_id !== Auth::id()) {
            abort(403);
        }

        $path = $application->user_cv;
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $application->delete();
        if ($job->number_of_applications > 0) {
            $job->decrement('number_of_applications');
        }

        return redirect()
            ->route('provider.jobs.applicants', $job->id)
            ->with('success', 'Application deleted successfully.');
    }
}

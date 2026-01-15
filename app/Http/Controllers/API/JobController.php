<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function featured(Request $request)
    {
        $perPage = (int) $request->query('per_page', 40);
        if ($perPage <= 0) {
            $perPage = 40;
        }

        $jobs = JobPost::with('category')
            ->latest()
            ->paginate($perPage);

        $payload = $jobs->getCollection()->map(function (JobPost $job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'nice_to_have' => $job->nice_to_have,
                'salary' => $job->salary,
                'type' => $job->type,
                'type_other' => $job->type_other,
                'type_name' => $job->category?->name,
                'deadline' => optional($job->deadline)->toDateString(),
                'onsite' => (bool) $job->onsite,
                'location' => $job->location,
                'number_of_applications' => $job->number_of_applications,
                'created_at' => $job->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'jobs' => $payload,
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'has_more' => $jobs->hasMorePages(),
            ],
        ]);
    }
}

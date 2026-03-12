<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerAppliedCitizensJob;
use App\Models\CustomerAppliedJob;
use App\Models\JobPostCitizen;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function status(Request $request, JobPost $job)
    {
        $applied = CustomerAppliedJob::where('user_id', $request->user()->id)
            ->where('job_id', $job->id)
            ->exists();

        return response()->json([
            'success' => true,
            'applied' => $applied,
        ]);
    }

    public function apply(Request $request, JobPost $job)
    {
        $existing = CustomerAppliedJob::where('user_id', $request->user()->id)
            ->where('job_id', $job->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job.',
            ], 409);
        }

        $maxCvKb = config('jobs.max_cv_kb', 10240);

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:255',
            'user_address' => 'required|string|max:255',
            'user_cv' => "required|file|mimes:pdf|max:{$maxCvKb}",
        ]);

        $cvPath = $request->file('user_cv')->store(
            "job_applications/{$request->user()->id}/{$job->id}",
            'public'
        );

        $application = CustomerAppliedJob::create([
            'user_id' => $request->user()->id,
            'job_id' => $job->id,
            'user_cv' => $cvPath,
            'user_address' => $validated['user_address'],
            'user_phone' => $validated['user_phone'],
            'user_email' => $validated['user_email'],
            'user_name' => $validated['user_name'],
        ]);

        $job->increment('number_of_applications');

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'application_id' => $application->id,
        ]);
    }

    public function statusCitizens(Request $request, JobPostCitizen $job)
    {
        $applied = CustomerAppliedCitizensJob::where('user_id', $request->user()->id)
            ->where('job_citizens_id', $job->id)
            ->exists();

        return response()->json([
            'success' => true,
            'applied' => $applied,
        ]);
    }

    public function applyCitizens(Request $request, JobPostCitizen $job)
    {
        $existing = CustomerAppliedCitizensJob::where('user_id', $request->user()->id)
            ->where('job_citizens_id', $job->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job.',
            ], 409);
        }

        $maxCvKb = config('jobs.max_cv_kb', 10240);

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'required|string|max:255',
            'user_address' => 'required|string|max:255',
            'user_cv' => "required|file|mimes:pdf|max:{$maxCvKb}",
            'password_image' => 'nullable|file|image|max:10240',
        ]);

        $cvPath = $request->file('user_cv')->store(
            "job_citizens_applications/{$request->user()->id}/{$job->id}",
            'public'
        );

        $passwordImagePath = '';
        if ($request->hasFile('password_image')) {
            $passwordImagePath = $request->file('password_image')->store(
                "job_citizens_applications/password_images/{$request->user()->id}/{$job->id}",
                'public'
            );
        }

        $application = CustomerAppliedCitizensJob::create([
            'owner_id' => $job->owner_id,
            'user_id' => $request->user()->id,
            'job_citizens_id' => $job->id,
            'user_cv' => $cvPath,
            'user_address' => $validated['user_address'],
            'user_phone' => $validated['user_phone'],
            'user_email' => $validated['user_email'],
            'user_name' => $validated['user_name'],
            'password_image' => $passwordImagePath,
        ]);

        $job->increment('number_of_applications');

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully.',
            'application_id' => $application->id,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display a listing of the merchant's reports.
     */
    public function index()
    {
        // TODO: Implement reports listing when reporting system is ready
        $reports = collect([]); // Empty collection for now
        
        return view('merchant.reports.index', compact('reports'));
    }

    /**
     * Display the specified report.
     */
    public function show($id)
    {
        // TODO: Implement report details view
        return view('merchant.reports.show');
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit($id)
    {
        // TODO: Implement report edit form
        return view('merchant.reports.edit');
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement report update
        return redirect()->route('merchant.reports.index')
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        // TODO: Implement report deletion
        return redirect()->route('merchant.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}

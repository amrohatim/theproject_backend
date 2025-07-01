<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the merchant's customers.
     */
    public function index()
    {
        // TODO: Implement customer listing when customer relationship system is ready
        $customers = collect([]); // Empty collection for now
        
        return view('merchant.customers.index', compact('customers'));
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        // TODO: Implement customer details view
        return view('merchant.customers.show');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        // TODO: Implement customer edit form
        return view('merchant.customers.edit');
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement customer update
        return redirect()->route('merchant.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        // TODO: Implement customer deletion
        return redirect()->route('merchant.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = max(1, min($request->integer('limit', 20), 100));

        $contacts = Contact::query()
            ->select('id', 'email', 'created_at', 'updated_at')
            ->latest('id')
            ->paginate($limit);

        return response()->json([
            'status' => 'success',
            'data' => $contacts->items(),
            'total' => $contacts->total(),
            'per_page' => $contacts->perPage(),
            'current_page' => $contacts->currentPage(),
            'last_page' => $contacts->lastPage(),
        ]);
    }
    public function SubmitContact(Request $request)
    {
        $email = $request->input('email');

        // Check email validate — dùng Eloquent thay raw SQL
        if (Contact::where('email', $email)->exists()) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'email' => ['Địa chỉ email này đã được sử dụng!']
                ]
            ], 422); 
        }

        // Insert data — dùng Eloquent
        Contact::create(['email' => $email]);

        // Report success to interface
        return response()->json([
            'status' => 'success',
            'message' => 'Bạn đã điền email chúng tôi sẽ sớm liên hệ với bạn sớm nhất!'
        ], 201); 
    }
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }
    public function destroy(Contact $contact)
    {
        //
    }
}

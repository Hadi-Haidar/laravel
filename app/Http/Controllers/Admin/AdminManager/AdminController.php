<?php

namespace App\Http\Controllers\Admin\AdminManager;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Constructor - restrict access to admin manager only
     */
    public function __construct()
    {
        // Remove the middleware call from here
       // $this->middleware(['auth:sanctum', 'admin.manager']);
    }
    
    /**
     * Display a listing of all admins.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $admins = Admin::all();
        return response()->json(['admins' => $admins]);
    }

    /**
     * Store a newly created admin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'role' => 'required|in:manager,seller',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $admin = Admin::create($validated);
        
        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin
        ], 201);
    }

    /**
     * Display the specified admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $admin = Admin::findOrFail($id);
        return response()->json(['admin' => $admin]);
    }

    /**
     * Update the specified admin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('admins')->ignore($id),
            ],
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|in:manager,seller',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }
        
        $admin->update($validated);
        
        return response()->json([
            'message' => 'Admin updated successfully',
            'admin' => $admin
        ]);
    }

    /**
     * Remove the specified admin from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent self-deletion
        if (auth()->id() == $id) {
            return response()->json([
                'message' => 'You cannot delete your own account'
            ], 403);
        }
        
        $admin->delete();
        
        return response()->json([
            'message' => 'Admin deleted successfully'
        ]);
    }
} 
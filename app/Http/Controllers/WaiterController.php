<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaiterController extends Controller
{
    public function index()
    {
        $ownerId = auth()->user()->effectiveOwnerId();
        $waiters = User::where('owner_id', $ownerId)
            ->where('role', 'waiter')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'created_at']);

        return response()->json(['waiters' => $waiters]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $ownerId = auth()->user()->effectiveOwnerId();
        $owner   = User::findOrFail($ownerId);

        $waiter = User::create([
            'name'                   => $request->name,
            'email'                  => $request->email,
            'password'               => Hash::make($request->password),
            'role'                   => 'waiter',
            'owner_id'               => $ownerId,
            'subscription_status'    => 'active',
            'subscription_expires_at'=> $owner->subscription_expires_at,
        ]);

        return response()->json([
            'success' => true,
            'waiter'  => $waiter->only(['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function destroy(User $user)
    {
        $ownerId = auth()->user()->effectiveOwnerId();
        if ($user->owner_id !== $ownerId || $user->role !== 'waiter') {
            abort(403);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}

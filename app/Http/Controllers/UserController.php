<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
            ->with(['school.county']) 
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($user) {
                $user->is_online = Cache::has('user-is-online-' . $user->id);
                return $user;
            });

        $schools = School::with('county')->orderBy('name', 'asc')->get();

        return view('dashboard.users.index', compact('users', 'schools'));
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json(['message' => 'User status updated successfully.']);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:super_admin,exam_admin,teacher'
        ]);
        
        $user->update(['role' => $request->role]);

        return response()->json(['message' => 'Role updated successfully.']);
    }

    public function updateSchool(Request $request, User $user)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id'
        ]);

        $user->update(['school_id' => $request->school_id]);

        return response()->json(['message' => 'School updated successfully']);
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully.']);
    }
}
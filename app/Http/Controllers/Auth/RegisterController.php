<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\County;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        $counties = County::all();
        return view('auth.register', compact('counties'));
    }

    public function getSchools($countyId)
    {
        $schools = School::where('county_id', $countyId)->get(['id', 'name']);
        return response()->json($schools);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'     => ['required', 'string', 'max:20'],
            'county_id' => ['required', 'exists:counties,id'],
            'school_id' => ['required', 'exists:schools,id'],
            'password'  => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'county_id'     => $validated['county_id'],
            'school_id'     => $validated['school_id'],
            'password'      => Hash::make($validated['password']),
            'is_active'     => true,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }
}
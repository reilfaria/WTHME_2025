<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nim'      => ['required', 'string', 'unique:users,nim'],
            'angkatan' => ['required', 'string'],
            'kelompok' => ['required', 'string'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'                 => $request->name,
            'nim'                  => $request->nim,
            'angkatan'             => $request->angkatan,
            'kelompok'             => $request->kelompok,
            'email'                => $request->email,
            'password'             => Hash::make($request->password),
            'role'                 => 'peserta',   // register publik SELALU peserta
           
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
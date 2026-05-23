<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nip' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'department' => ['required', 'string', Rule::in([
                'Dinas Pendidikan',
                'Dinas Kesehatan',
                'Dinas BKPSDM',
                'Dinas Sosial',
                'Dinas Dukcapil',
                'Inspektorat',
                'Dinas Perizinan',
                'Dinas Kecamatan',
                'Dinas Bappelitbangda',
                
            ])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'department' => $request->department,
            'password' => Hash::make($request->password),
            'role' => 'pegawai-dinas',
            'phone' => $request->phone,
        ]);

        $user->assignRole('pegawai-dinas');

        event(new Registered($user));

        return redirect(route('login', absolute: false));
    }
}

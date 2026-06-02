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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'numeric', 'min_digits:10', 'max_digits:15'],
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
        ], [
            'email.unique' => 'Email sudah terdaftar.',
            'phone.numeric' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'phone.min_digits' => 'Nomor WhatsApp minimal 10 digit.',
            'phone.max_digits' => 'Nomor WhatsApp maksimal 15 digit.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => strtolower(explode('@', $request->email)[0] . rand(1000, 9999)),
            'phone' => $request->phone,
            'role' => 1,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect(route('login', absolute: false))->with('status', 'Akun berhasil dibuat. Silakan masuk menggunakan email dan password yang telah didaftarkan.');
    }

    /**
     * Check if email is available for registration via AJAX.
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        if (!$email) {
            return response()->json(['available' => false]);
        }

        $exists = User::where('email', $email)->exists();
        return response()->json(['available' => !$exists]);
    }
}

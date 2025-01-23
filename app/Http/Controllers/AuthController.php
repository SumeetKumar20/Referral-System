<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cookie as CookieModel; // Use the Cookie model

class AuthController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Store referral code and link in cookies
        $referralCode = uniqid();
        $referralLink = url('/register?ref=' . $referralCode);
        
        // Set cookies
        Cookie::queue('referral_code', $referralCode, 43200); // 30 days
        Cookie::queue('referral_link', $referralLink, 43200); // 30 days

        // Save cookies in the database
        CookieModel::create(['name' => 'referral_code', 'value' => $referralCode]);
        CookieModel::create(['name' => 'referral_link', 'value' => $referralLink]);

        return redirect(RouteServiceProvider::HOME);
    }
}

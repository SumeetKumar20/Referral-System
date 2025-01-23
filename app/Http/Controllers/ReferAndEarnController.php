<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; // For database operations
use App\Models\User; // If you want to use the User model directly

class ReferAndEarnController extends Controller
{
    public function syncUserToUnocuePro($userId)
    {
        // Get the user from the referandearn database
        $referUser = DB::table('users')->where('id', $userId)->first();

        if ($referUser) {
            // Check if the user exists in Unocue Pro database
            $unocueUser = DB::connection('unocuepro')->table('users')->where('email', $referUser->email)->first();

            if ($unocueUser) {
                // Update the existing user in Unocue Pro
                DB::connection('unocuepro')->table('users')->where('email', $referUser->email)->update([
                    'username' => $referUser->username,
                    'refercode' => $referUser->refercode,
                ]);
            } else {
                // Create a new user in Unocue Pro
                DB::connection('unocuepro')->table('users')->insert([
                    'username' => $referUser->username,
                    'email' => $referUser->email,
                    'password' => $referUser->password,  // Already hashed
                    'refercode' => $referUser->refercode,
                ]);
            }
        }
    }
}


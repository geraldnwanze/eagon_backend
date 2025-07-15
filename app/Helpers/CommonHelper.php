<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommonHelper
{
    public static function generateQrCode($id)
    {
        return "https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=$id";
    }

    public static function generateInvitationCode($length = 9)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';

        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[random_int(0, $characters_length - 1)];
        }

        // Append a unique identifier (like the current timestamp)
        return strtoupper($random_string . time());
    }

    public static function generateOTP(User|Authenticatable|Model $user): int
    {
        $otp = mt_rand(100000, 999999);
        DB::table('o_t_p_s')->updateOrInsert([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'tenant_key' => $user->tenant_key
        ], [
            'code' => $otp,
            'expires_at' => now()->addMinutes(10)
        ]);

        return $otp;
    }
}

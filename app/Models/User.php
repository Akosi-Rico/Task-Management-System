<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard_name = "web";

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function registerNewUser($request)
    {
        try {
            DB::beginTransaction();

            if (empty($request)) {
                return false;
            }

            self::create([
                "name" => request()->payload["name"],
                "email" => request()->payload["email"],
                "password" => Hash::make(request()->payload["password"]),
            ]);

            DB::commit();

            auth()->attempt(["email" => request()->payload["email"], "password" => request()->payload["password"]]);

            return response()->json([
                "message" => "Transaction Successfully Completed!",
            ], Response::HTTP_OK);
        } catch(\Throwable $th) {
            DB::rollback();
            return response()->json([
                "message" => $th->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public static function login($request) 
    {
        try {
            if (empty($request)) {
                return false;
            }

            auth()->attempt(["email" => $request["email"], "password" => $request["password"]]);
            if (empty(auth()->check())) {
                return response()->json([
                    "message" => "Email & Password given is not registered.",
                ], Response::HTTP_BAD_REQUEST);
            }

            return response()->json([
                "message" => "Successfully Login!",
            ], Response::HTTP_OK);
        } catch(\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}

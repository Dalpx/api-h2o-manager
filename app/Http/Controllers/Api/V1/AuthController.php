<?php

namespace App\Http\Controllers\Api\V1;

use App\http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use function Illuminate\Support\hours;
use function Symfony\Component\Clock\now;

class AuthController extends Controller
{
    //Register method

    public function register(Request $request){
        $validated = $request->validate([
            'name'=> 'required|string|max:255',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|string|min:8|confirmed',
        ]
        );

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        $rol = $user->getRoleName();
        //Generate token
        $token = $user->createToken('auth_token', [$rol])->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function login(Request $request){
        $validated = $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]
        );
        
        $user = User::where('email', $validated['email'])->first();

        $user->tokens()->delete();

        if (!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Credenciales inválidas'
            ]);
        }

        $rol = $user->getRoleName();

        $token = $user->createToken('access_token', [$rol])->plainTextToken;
        
        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function logout(Request $request){
        $request->user('sanctum')->currentAccessToken()->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Logged out succesfully.'

        ]);
    
        }
}

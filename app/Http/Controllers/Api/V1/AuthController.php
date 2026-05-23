<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\AuthUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /*public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json([
                'message' => 'Correo o contraseña incorrectos.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->tokens()->delete();
        $user->load(['rol', 'sucursal']);
        $token = $user->createToken('spa-login')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new AuthUserResource($user),
        ]);
    }*/

    public function login(LoginRequest $request): JsonResponse
    {
        // Retorna el array con los campos validados
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        // $credentials['password'] entrega el string exacto que Hash::check necesita
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Correo o contraseña incorrectos.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user->tokens()->delete();
        $user->load(['rol', 'sucursal']);
        $token = $user->createToken('spa-login')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new AuthUserResource($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['rol', 'sucursal']);

        return response()->json([
            'user' => new AuthUserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}

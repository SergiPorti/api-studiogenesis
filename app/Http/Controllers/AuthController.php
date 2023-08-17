<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'filled|string|email',
            'username' => 'filled|string',
            'password' => 'required|string|confirmed',
        ]);

        if (!Auth::attempt($request->only('email', 'username', 'password'))) {
            return response()->json(["data" => ['message' => 'Invalid credentials']], 401);
        }
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->username)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "data" => [
                'message' => 'S\'ha accedit correctament al compte',
                'token' => $token,
                'user' => $user
            ]
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al actualitzar la contrassenya']], 422);
            }
            $user = $request->user();
            $user->update([
                'password' => Hash::make($request->get('password'))
            ]);

            return response()->json(['data' => ["message" => 'Contrassenya restablerta correctament']], 200);
        } catch (\Exception $e) {
            return response()->json(["data" => ["error_message" => $e, "message" => "Error al restablir la contrassenya"]], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(["data" => ["message" => "Sessió tancada correctament"]], 200);
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'lastname' => 'required|string',
                'birthdate' => 'required|string',
                'username' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al registrarse']], 422);
            }
            $validatedData = $request->all();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                "data" => [
                    'message' => 'Usuari creat correctament',
                    'token' => $token,
                    'user' => $user,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(["data" => ['error_message' => $e, 'message' => 'Error al crear l\'usuari, el correu electrònic o el nom ja estan en ús']], 500);
        }
    }
}

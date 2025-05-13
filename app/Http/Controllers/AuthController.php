<?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;
   use App\Models\User;
   use Illuminate\Support\Facades\Hash;
   use Illuminate\Validation\ValidationException;
   use Illuminate\Support\Facades\Password;

   class AuthController extends Controller
   {
       // Registro de usuario
       public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
        'is_first_login' => true,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Usuario registrado exitosamente',
        'token' => $token,
        'user' => $user,
        'redirect' => '/inicio' // Redirección a /inicio tras registro
    ], 201);
}

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    if ($user->is_first_login) {
        $user->is_first_login = false;
        $user->save();
    }

    return response()->json([
        'user' => $user,
        'token' => $token,
        'redirect' => '/inicio' // Redirección a /inicio para todos los logins
    ]);
  }


  public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Sesión cerrada exitosamente'
    ]);
}

}

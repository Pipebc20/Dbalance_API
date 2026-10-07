<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        $clientId = config('services.google.client_id');

        if (!$clientId) {
            return response()->json([
                'message' => 'El inicio de sesión con Google no está configurado.',
            ], 500);
        }

        // Google valida la firma y la vigencia del token.
        // Aquí revisamos que el token sea para esta app y que el correo esté verificado.
        $respuesta = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->input('credential'),
        ]);

        if (!$respuesta->successful()) {
            return response()->json([
                'message' => 'No se pudo validar la cuenta de Google.',
            ], 401);
        }

        $datos = $respuesta->json();

        $paraEstaApp = ($datos['aud'] ?? null) === $clientId;
        $emisorValido = in_array($datos['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true);
        $correoVerificado = filter_var($datos['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $email = $datos['email'] ?? null;

        if (!$paraEstaApp || !$emisorValido || !$correoVerificado || !$email) {
            return response()->json([
                'message' => 'La cuenta de Google no es válida.',
            ], 401);
        }

        // Si el correo ya tiene cuenta (registro normal), entra a esa misma cuenta.
        // La búsqueda ignora mayúsculas, porque algunos correos se registraron así.
        $user = User::whereRaw('LOWER(email) = ?', [Str::lower($email)])->first();

        if (!$user) {
            $user = new User();
            $user->name = Str::limit($datos['name'] ?? Str::before($email, '@'), 100, '');
            $user->email = Str::lower($email);
            $user->password = Hash::make(Str::random(40)); // nadie la conoce; se puede cambiar con "Olvidé mi contraseña"
            $user->email_verified_at = now();
            $user->is_first_login = true; // igual que en el registro normal
            $user->save();
        } elseif ($user->is_first_login) {
            // igual que en el login normal
            $user->is_first_login = false;
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'user' => $user,
            'redirect' => '/inicio',
        ], 200);
    }
}

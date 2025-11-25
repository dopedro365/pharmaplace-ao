<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class CustomLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.custom-login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Pode ser email ou telefone
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $login = $request->input('login');
        $password = $request->input('password');

        // Determinar se é email ou telefone
        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Tentar autenticar
        $credentials = [
            $loginType => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Verificar se a conta está aprovada
            if ($user->status !== 'approved') {
                return redirect()->route('account.pending');
            }

            // Redirecionamento inteligente
            $intendedUrl = $request->session()->get('url.intended');
            
            if ($intendedUrl) {
                return redirect($intendedUrl);
            }

            // Se for admin, manager ou pharmacy, redirecionar para painel
            if (in_array($user->role, ['admin', 'manager', 'pharmacy'])) {
                return redirect()->route('filament.painel.pages.dashboard');
            }

            // Caso contrário, redirecionar para home
            return redirect()->route('home');
        }

        // Se falhou, verificar se o usuário existe para dar feedback específico
        $user = User::where($loginType, $login)->first();
        
        if (!$user) {
            $errorMessage = $loginType === 'email' 
                ? 'Email não encontrado.' 
                : 'Número de telefone não encontrado.';
        } else {
            $errorMessage = 'Senha incorreta.';
        }

        throw ValidationException::withMessages([
            'login' => $errorMessage,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

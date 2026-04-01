<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $users;

    protected $activityLogs;

    public function __construct(UserRepository $users, ActivityLogService $activityLogs)
    {
        $this->users = $users;
        $this->activityLogs = $activityLogs;
    }

    public function login($login, $password, $remember = false)
    {
        $user = $this->users->findByLogin($login);

        if (!$user || !$user->is_active || !Hash::check($password, $user->password)) {
            return [false, 'Las credenciales no son válidas.'];
        }

        Auth::login($user, $remember);

        $user->last_login_at = now();
        $user->save();

        $this->activityLogs->log('auth', 'login', 'Inicio de sesión exitoso', $user->id, null, [
            'user_id' => $user->id,
            'email' => $user->email,
        ], $user->id);

        return [true, $user];
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $this->activityLogs->log('auth', 'logout', 'Cierre de sesión', $user->id, null, null, $user->id);
        }

        Auth::logout();
    }
}

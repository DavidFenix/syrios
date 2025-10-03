<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('dashboard_route')) {
    function dashboard_route()
    {
        $user = auth()->user();

        if (!$user) {
            return route('login');
        }

        // se não tem contexto na sessão, não tenta adivinhar → manda para login
        if (!session('current_role')) {
            return route('login');
        }

        // 👉 Verifica se contexto já foi definido na sessão
        $role = session('current_role');
        $schoolId = session('current_school_id');

        if ($role && $schoolId) {
            return match ($role) {
                'master'     => route('master.dashboard'),
                'secretaria' => route('secretaria.dashboard'),
                'escola'     => route('escola.dashboard'),
                'professor'  => route('professor.dashboard'),
                //'professor'  => '/', // 🔥 por enquanto vai para home ou página neutra
                default      => '/',
            };
        }

        // 👉 Fallback: se não tiver contexto na sessão,
        // mas o usuário tem papéis, tenta inferir
        if ($user->hasRole('master')) {
            return route('master.dashboard');
        }

        if ($user->hasRole('secretaria')) {
            return route('secretaria.dashboard');
        }

        if ($user->hasRole('escola')) {
            return route('escola.dashboard');
        }

        if ($user->hasRole('professor')) {
            return route('professor.dashboard');
            //return '/'; // 🔥 ou rota genérica de professores quando existir
        }

        // 👉 Último recurso: pedir para escolher contexto
        return route('choose.school');
    }
}



/*
use Illuminate\Support\Facades\Route;

if (!function_exists('dashboard_route')) {
    function dashboard_route()
    {

        $user = auth()->user();

        if (!$user) {
            return route('login');
        }

        if ($user->hasRole('master')) {
            return route('master.dashboard');
        }

        if ($user->hasRole('secretaria')) {
            return route('secretaria.dashboard');
        }

        return route('escola.usuarios.index');
    }
}
*/